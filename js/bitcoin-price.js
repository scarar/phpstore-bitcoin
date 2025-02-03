let lastPrice = null;
let lastUpdateTime = 0;
const UPDATE_INTERVAL = 60000; // 1 minute, blockchain.info updates every minute

function updatePriceDisplay(btcPrice) {
    // Update all price elements
    document.querySelectorAll('.btc-price').forEach(el => {
        const usdPrice = parseFloat(el.getAttribute('data-usd'));
        const btcAmount = usdPrice / btcPrice;
        el.textContent = btcAmount.toFixed(8) + ' BTC';
    });

    // Update the price display in header
    const priceDisplay = document.getElementById('btc-usd-rate');
    if (priceDisplay) {
        priceDisplay.innerHTML = `
            <i class="fab fa-bitcoin"></i>
            $${btcPrice.toLocaleString()} USD
        `;
    }
}

function updateBitcoinPrice() {
    const now = Date.now();
    
    // Use cached price if less than UPDATE_INTERVAL has passed
    if (lastPrice && (now - lastUpdateTime) < UPDATE_INTERVAL) {
        updatePriceDisplay(lastPrice);
        return;
    }

    // Try Binance.us first
    fetch('https://api.binance.us/api/v3/ticker/price?symbol=BTCUSDT')
        .then(response => {
            if (!response.ok) {
                throw new Error('Binance.us API failed');
            }
            return response.json();
        })
        .then(data => {
            const btcPrice = parseFloat(data.price);
            lastPrice = btcPrice;
            lastUpdateTime = now;
            updatePriceDisplay(btcPrice);

            // Store price in localStorage as backup
            localStorage.setItem('btcPrice', btcPrice);
            localStorage.setItem('btcPriceTimestamp', now);
        })
        .catch(error => {
            console.warn('Binance.us API failed, trying Binance global:', error);
            
            // Try Binance global as fallback
            return fetch('https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Binance global API failed');
                    }
                    return response.json();
                })
                .then(data => {
                    const btcPrice = parseFloat(data.price);
                    lastPrice = btcPrice;
                    lastUpdateTime = now;
                    updatePriceDisplay(btcPrice);

                    localStorage.setItem('btcPrice', btcPrice);
                    localStorage.setItem('btcPriceTimestamp', now);
                })
                .catch(error2 => {
                    console.warn('Binance global API failed, trying Blockchain.info:', error2);
                    
                    // Try Blockchain.info as final fallback
                    return fetch('https://blockchain.info/ticker')
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Blockchain.info API failed');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data && data.USD) {
                                const btcPrice = data.USD.last;
                                lastPrice = btcPrice;
                                lastUpdateTime = now;
                                updatePriceDisplay(btcPrice);

                                localStorage.setItem('btcPrice', btcPrice);
                                localStorage.setItem('btcPriceTimestamp', now);
                            }
                        });
                });
        })
        .catch(error => {
            console.error('Error fetching Bitcoin price:', error);
            // Try to use stored price if available
            const storedPrice = localStorage.getItem('btcPrice');
            const storedTimestamp = localStorage.getItem('btcPriceTimestamp');
            
            if (storedPrice && storedTimestamp) {
                const age = now - parseInt(storedTimestamp);
                if (age < 3600000) { // Use stored price if less than 1 hour old
                    updatePriceDisplay(parseFloat(storedPrice));
                }
            }
        });
}

// Initial update
document.addEventListener('DOMContentLoaded', () => {
    // Load Font Awesome for Bitcoin icon if not already loaded
    if (!document.querySelector('link[href*="font-awesome"]')) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css';
        document.head.appendChild(link);
    }

    // Try to use stored price immediately while fetching new price
    const storedPrice = localStorage.getItem('btcPrice');
    if (storedPrice) {
        updatePriceDisplay(parseFloat(storedPrice));
    }

    // Fetch fresh price
    updateBitcoinPrice();
});

// Update price every minute
setInterval(updateBitcoinPrice, UPDATE_INTERVAL);

// Update when page becomes visible
document.addEventListener('visibilitychange', () => {
    if (!document.hidden) {
        updateBitcoinPrice();
    }
});