// API Configuration
const APIS = {
    BLOCKCHAIN: {
        price: 'https://blockchain.info/ticker',
        convert: (value) => `https://blockchain.info/tobtc?currency=USD&value=${value}`
    },
    BINANCE_US: {
        price: 'https://api.binance.us/api/v3/ticker/price?symbol=BTCUSDT'
    },
    BINANCE_GLOBAL: {
        price: 'https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT'
    },
    COINGECKO: {
        price: 'https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=usd'
    }
};

let currentPrice = null;
let lastUpdateTime = 0;
const UPDATE_INTERVAL = 60000; // 1 minute
const CACHE_DURATION = 300000; // 5 minutes

// Helper function to format numbers
function formatNumber(num, decimals = 8) {
    return Number(num).toLocaleString('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
}

function updatePriceDisplay(btcPrice, source = 'Unknown') {
    currentPrice = btcPrice;
    lastUpdateTime = Date.now();
    
    // Store in localStorage
    localStorage.setItem('btcPrice', btcPrice);
    localStorage.setItem('btcPriceTimestamp', lastUpdateTime);
    localStorage.setItem('btcPriceSource', source);

    // Update all price elements
    document.querySelectorAll('.btc-price').forEach(async el => {
        const usdPrice = parseFloat(el.getAttribute('data-usd'));
        
        try {
            // Try blockchain.info conversion first
            const response = await fetch(APIS.BLOCKCHAIN.convert(usdPrice));
            if (!response.ok) throw new Error('Conversion failed');
            const btcAmount = await response.text();
            el.textContent = btcAmount + ' BTC';
        } catch (error) {
            // Fallback to manual calculation using our stored price
            if (currentPrice) {
                const btcAmount = usdPrice / currentPrice;
                el.textContent = formatNumber(btcAmount) + ' BTC';
            }
        }
    });

    // Update the header price display
    const priceDisplay = document.getElementById('btc-usd-rate');
    if (priceDisplay) {
        priceDisplay.innerHTML = `
            <div class="btc-price-display" title="Source: ${source}">
                <i class="fab fa-bitcoin"></i>
                <span class="btc-price-value">$${formatNumber(btcPrice, 2)}</span>
                <span class="btc-price-currency">USD</span>
            </div>
        `;
    }
}

// Function to get price from an API
async function fetchPrice(api, parser) {
    const response = await fetch(api);
    if (!response.ok) throw new Error('API request failed');
    const data = await response.json();
    return parser(data);
}

// Price parsers for different APIs
const PRICE_PARSERS = {
    blockchain: (data) => ({ price: data.USD.last, source: 'Blockchain.info' }),
    binance: (data) => ({ price: parseFloat(data.price), source: 'Binance' }),
    coingecko: (data) => ({ price: data.bitcoin.usd, source: 'CoinGecko' })
};

async function updateBitcoinPrice() {
    const now = Date.now();
    
    // Check if we need to update
    if (currentPrice && (now - lastUpdateTime) < UPDATE_INTERVAL) {
        return;
    }

    // Try to load from localStorage if recent enough
    const storedPrice = localStorage.getItem('btcPrice');
    const storedTimestamp = localStorage.getItem('btcPriceTimestamp');
    const storedSource = localStorage.getItem('btcPriceSource');
    
    if (storedPrice && storedTimestamp && (now - storedTimestamp) < CACHE_DURATION) {
        updatePriceDisplay(parseFloat(storedPrice), storedSource + ' (cached)');
    }

    // Array of API attempts, in order of preference
    const attempts = [
        {
            name: 'Blockchain.info',
            fetch: () => fetchPrice(APIS.BLOCKCHAIN.price, PRICE_PARSERS.blockchain)
        },
        {
            name: 'Binance US',
            fetch: () => fetchPrice(APIS.BINANCE_US.price, PRICE_PARSERS.binance)
        },
        {
            name: 'Binance Global',
            fetch: () => fetchPrice(APIS.BINANCE_GLOBAL.price, PRICE_PARSERS.binance)
        },
        {
            name: 'CoinGecko',
            fetch: () => fetchPrice(APIS.COINGECKO.price, PRICE_PARSERS.coingecko)
        }
    ];

    // Try each API in sequence until one works
    for (const attempt of attempts) {
        try {
            console.log(`Trying ${attempt.name} API...`);
            const { price, source } = await attempt.fetch();
            updatePriceDisplay(price, source);
            console.log(`Successfully updated price from ${attempt.name}`);
            return;
        } catch (error) {
            console.warn(`${attempt.name} API failed:`, error);
            continue;
        }
    }

    // If all APIs fail, show error and use cached price if available
    console.error('All APIs failed');
    const priceDisplay = document.getElementById('btc-usd-rate');
    if (priceDisplay) {
        if (currentPrice) {
            updatePriceDisplay(currentPrice, 'Cached (All APIs Failed)');
        } else {
            priceDisplay.innerHTML = `
                <div class="btc-price-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    Price Unavailable
                </div>
            `;
        }
    }
}

// Initialize price updates
document.addEventListener('DOMContentLoaded', () => {
    // Try to show cached price immediately while fetching new price
    const storedPrice = localStorage.getItem('btcPrice');
    const storedTimestamp = localStorage.getItem('btcPriceTimestamp');
    const storedSource = localStorage.getItem('btcPriceSource');
    
    if (storedPrice && storedTimestamp) {
        const age = Date.now() - parseInt(storedTimestamp);
        if (age < CACHE_DURATION) {
            updatePriceDisplay(parseFloat(storedPrice), storedSource + ' (cached)');
        }
    }

    // Start fetching fresh price
    updateBitcoinPrice();
});

// Update price periodically
setInterval(updateBitcoinPrice, UPDATE_INTERVAL);

// Update when page becomes visible
document.addEventListener('visibilitychange', () => {
    if (!document.hidden) {
        updateBitcoinPrice();
    }
});
