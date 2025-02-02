<?php
session_start();
include("../partials/connect.php");
require_once '../vendor/autoload.php';
use Denpa\Bitcoin\Client as BitcoinClient;

if (!isset($_SESSION['customerid'])) {
    header('Location: ../customerforms.php');
    exit;
}

// Initialize Bitcoin client
$bitcoind = new BitcoinClient([
    'scheme'   => 'http',
    'host'     => 'localhost',
    'port'     => 8332,
    'user'     => 'bitcoin',
    'password' => 'local321'
]);

$message = '';
$btc_address = '';
$balance = 0;

// Get customer's Bitcoin address and balance
$stmt = $connect->prepare("SELECT username, btc_address, balance FROM customers WHERE id = ?");
$stmt->bind_param("i", $_SESSION['customerid']);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $btc_address = $row['btc_address'];
    $balance = $row['balance'];
    $username = $row['username'];
}

// Handle withdrawal request
if (isset($_POST['withdraw']) && isset($_POST['amount']) && isset($_POST['address']) && isset($_POST['pin'])) {
    $amount = floatval($_POST['amount']);
    $address = $_POST['address'];
    $pin = $_POST['pin'];

    // Verify PIN
    $stmt = $connect->prepare("SELECT pin FROM customers WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['customerid']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($pin !== $user['pin']) {
        $message = '<div class="alert alert-danger">Invalid PIN</div>';
    } else if ($amount <= 0) {
        $message = '<div class="alert alert-danger">Invalid amount</div>';
    } else if ($amount > $balance) {
        $message = '<div class="alert alert-danger">Insufficient balance</div>';
    } else {
        try {
            // Send Bitcoin
            $txid = $bitcoind->wallet($username)->sendtoaddress($address, $amount);
            
            if ($txid->get()) {
                // Update balance in database
                $new_balance = $balance - $amount;
                $stmt = $connect->prepare("UPDATE customers SET balance = ? WHERE id = ?");
                $stmt->bind_param("di", $new_balance, $_SESSION['customerid']);
                $stmt->execute();

                // Record withdrawal in database
                $stmt = $connect->prepare("INSERT INTO withdrawals (customer_id, amount, address, txid, status) VALUES (?, ?, ?, ?, 'completed')");
                $stmt->bind_param("idss", $_SESSION['customerid'], $amount, $address, $txid->get());
                $stmt->execute();

                $message = '<div class="alert alert-success">Withdrawal successful! Transaction ID: ' . $txid->get() . '</div>';
                $balance = $new_balance;
            }
        } catch (Exception $e) {
            $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        }
    }
}

// Get withdrawal history
$stmt = $connect->prepare("SELECT * FROM withdrawals WHERE customer_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt->bind_param("i", $_SESSION['customerid']);
$stmt->execute();
$withdrawals = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<?php include("../partials/head.php"); ?>
<body class="animsition">
    <?php include("../partials/header.php"); ?>

    <section class="bg-img1 txt-center p-lr-15 p-tb-92" style="background-image: url('../images/bg-01.jpg');">
        <h2 class="ltext-105 cl0 txt-center">Bitcoin Wallet</h2>
    </section>

    <section class="bg0 p-t-104 p-b-116">
        <div class="container">
            <div class="flex-w flex-tr">
                <div class="size-210 bor10 p-lr-70 p-t-55 p-b-70 p-lr-15-lg w-full-md">
                    <h4 class="mtext-105 cl2 txt-center p-b-30">Your Bitcoin Address</h4>
                    
                    <div class="m-b-20">
                        <label class="stext-110">Deposit Address:</label>
                        <div class="bor8 m-b-20 how-pos4-parent">
                            <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30" type="text" value="<?php echo $btc_address; ?>" readonly>
                            <button class="how-pos4 pointer" onclick="copyAddress()">Copy</button>
                        </div>
                    </div>

                    <div class="m-b-20">
                        <label class="stext-110">Current Balance:</label>
                        <div class="bor8 m-b-20 how-pos4-parent">
                            <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30" type="text" value="<?php echo number_format($balance, 8); ?> BTC" readonly>
                        </div>
                    </div>

                    <div class="m-b-20">
                        <h4 class="mtext-105 cl2 p-b-30">Recent Withdrawals</h4>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($withdrawals as $withdrawal): ?>
                                <tr>
                                    <td><?php echo date('Y-m-d H:i', strtotime($withdrawal['created_at'])); ?></td>
                                    <td><?php echo number_format($withdrawal['amount'], 8); ?> BTC</td>
                                    <td><?php echo substr($withdrawal['address'], 0, 10) . '...'; ?></td>
                                    <td><?php echo $withdrawal['status']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="size-210 bor10 p-lr-70 p-t-55 p-b-70 p-lr-15-lg w-full-md">
                    <form method="POST">
                        <h4 class="mtext-105 cl2 txt-center p-b-30">Withdraw Bitcoin</h4>

                        <?php echo $message; ?>

                        <div class="m-b-20">
                            <label class="stext-110">Amount (BTC):</label>
                            <div class="bor8 m-b-20 how-pos4-parent">
                                <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30" type="number" step="0.00000001" name="amount" required>
                            </div>
                        </div>

                        <div class="m-b-20">
                            <label class="stext-110">Destination Address:</label>
                            <div class="bor8 m-b-20 how-pos4-parent">
                                <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30" type="text" name="address" required>
                            </div>
                        </div>

                        <div class="m-b-20">
                            <label class="stext-110">PIN Code:</label>
                            <div class="bor8 m-b-20 how-pos4-parent">
                                <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30" type="password" name="pin" required>
                            </div>
                        </div>

                        <button class="flex-c-m stext-101 cl0 size-121 bg3 bor1 hov-btn3 p-lr-15 trans-04 pointer" name="withdraw">
                            Withdraw
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include("../partials/footer.php"); ?>

    <script>
        function copyAddress() {
            const address = '<?php echo $btc_address; ?>';
            navigator.clipboard.writeText(address).then(() => {
                alert('Address copied to clipboard!');
            });
        }
    </script>
</body>
</html>