/**
 * Vibe Graphics - Premium GPU Store
 * Centralized Application Logic & Cross-Page State
 */

// ==========================================================================
// 1. Procedural Vector GPU Artwork Generator
// ==========================================================================
const gpuSvgMeta = {
    rtx5090: { name: 'RTX 5090', series: 'BLACKWELL', vram: '32GB GDDR7 • 512-BIT', brand: 'nvidia', fans: 3, glow: '#eab308', secGlow: '#c084fc', border: '#facc15' },
    rtx5080: { name: 'RTX 5080', series: 'BLACKWELL', vram: '16GB GDDR7 • 256-BIT', brand: 'nvidia', fans: 3, glow: '#38bdf8', secGlow: '#818cf8', border: '#38bdf8' },
    rtx5070ti: { name: 'RTX 5070 Ti', series: 'BLACKWELL', vram: '16GB GDDR7 • 256-BIT', brand: 'nvidia', fans: 3, glow: '#34d399', secGlow: '#8c52ff', border: '#34d399' },
    rtx5070: { name: 'RTX 5070', series: 'BLACKWELL', vram: '12GB GDDR7 • 192-BIT', brand: 'nvidia', fans: 2, glow: '#60a5fa', secGlow: '#c084fc', border: '#60a5fa' },
    rtx4070s: { name: 'RTX 4070 SUPER', series: 'ADA LOVELACE', vram: '12GB GDDR6X • 192-BIT', brand: 'nvidia', fans: 2, glow: '#c084fc', secGlow: '#f472b6', border: '#a855f7' },
    rtx4060: { name: 'RTX 4060', series: 'ADA LOVELACE', vram: '8GB GDDR6 • 128-BIT', brand: 'nvidia', fans: 2, glow: '#4ade80', secGlow: '#22c55e', border: '#4ade80' },
    rtx3060: { name: 'RTX 3060', series: 'AMPERE', vram: '12GB GDDR6 • 192-BIT', brand: 'nvidia', fans: 2, glow: '#fbbf24', secGlow: '#f59e0b', border: '#fbbf24' },
    rx7900xtx: { name: 'RX 7900 XTX', series: 'RDNA 3 CHIPLET', vram: '24GB GDDR6 • 384-BIT', brand: 'amd', fans: 3, glow: '#f43f5e', secGlow: '#fb7185', border: '#ef4444' },
    rx7800xt: { name: 'RX 7800 XT', series: 'RDNA 3 CHIPLET', vram: '16GB GDDR6 • 256-BIT', brand: 'amd', fans: 2, glow: '#fb7185', secGlow: '#fda4af', border: '#e11d48' },
    rtx4090: { name: 'RTX 4090', series: 'ADA LOVELACE', vram: '24GB GDDR6X • 384-BIT', brand: 'nvidia', fans: 3, glow: '#c084fc', secGlow: '#e2e8f0', border: '#a855f7' }
};

function getGpuSvg(key) {
    const d = gpuSvgMeta[key] || { name: key || 'GPU', series: 'ADVANCED ARCHITECTURE', vram: 'HIGH PERFORMANCE VRAM', brand: 'nvidia', fans: 3, glow: '#8c52ff', secGlow: '#c084fc', border: '#8c52ff' };
    const isAMD = d.brand === 'amd';
    const brandText = isAMD ? 'RADEON' : 'GEFORCE RTX';

    let fansSvg = '';
    if (d.fans === 3) {
        const positions = [140, 300, 460];
        positions.forEach((cx) => {
            fansSvg += `
            <g transform="translate(${cx}, 145)">
                <circle r="52" fill="#120e24" stroke="rgba(255,255,255,0.08)" stroke-width="2"/>
                <line x1="-40" y1="-30" x2="40" y2="-30" stroke="rgba(255,255,255,0.06)" stroke-width="1.5"/>
                <line x1="-48" y1="-15" x2="48" y2="-15" stroke="rgba(255,255,255,0.06)" stroke-width="1.5"/>
                <line x1="-50" y1="0" x2="50" y2="0" stroke="rgba(255,255,255,0.06)" stroke-width="1.5"/>
                <line x1="-48" y1="15" x2="48" y2="15" stroke="rgba(255,255,255,0.06)" stroke-width="1.5"/>
                <line x1="-40" y1="30" x2="40" y2="30" stroke="rgba(255,255,255,0.06)" stroke-width="1.5"/>
                <circle r="48" fill="none" stroke="${d.glow}" stroke-width="2.5" opacity="0.85" filter="url(#glowFilter)"/>
                <path d="M0,0 L-6,-42 A46,46 0 0,1 18,-38 Z" fill="url(#bladeGrad)" opacity="0.9"/>
                <path d="M0,0 L36,-22 A46,46 0 0,1 42,4 Z" fill="url(#bladeGrad)" opacity="0.9"/>
                <path d="M0,0 L38,18 A46,46 0 0,1 18,39 Z" fill="url(#bladeGrad)" opacity="0.9"/>
                <path d="M0,0 L-18,38 A46,46 0 0,1 -40,18 Z" fill="url(#bladeGrad)" opacity="0.9"/>
                <path d="M0,0 L-42,-2 A46,46 0 0,1 -28,-32 Z" fill="url(#bladeGrad)" opacity="0.9"/>
                <circle r="16" fill="url(#hubGrad)" stroke="rgba(255,255,255,0.25)" stroke-width="1.5"/>
                <circle r="7" fill="${d.glow}" opacity="0.9"/>
            </g>`;
        });
    } else {
        const positions = [190, 410];
        positions.forEach((cx) => {
            fansSvg += `
            <g transform="translate(${cx}, 145)">
                <circle r="66" fill="#120e24" stroke="rgba(255,255,255,0.08)" stroke-width="2"/>
                <line x1="-54" y1="-38" x2="54" y2="-38" stroke="rgba(255,255,255,0.06)" stroke-width="1.5"/>
                <line x1="-60" y1="-19" x2="60" y2="-19" stroke="rgba(255,255,255,0.06)" stroke-width="1.5"/>
                <line x1="-64" y1="0" x2="64" y2="0" stroke="rgba(255,255,255,0.06)" stroke-width="1.5"/>
                <line x1="-60" y1="19" x2="60" y2="19" stroke="rgba(255,255,255,0.06)" stroke-width="1.5"/>
                <line x1="-54" y1="38" x2="54" y2="38" stroke="rgba(255,255,255,0.06)" stroke-width="1.5"/>
                <circle r="62" fill="none" stroke="${d.glow}" stroke-width="3" opacity="0.85" filter="url(#glowFilter)"/>
                <path d="M0,0 L-8,-56 A60,60 0 0,1 24,-52 Z" fill="url(#bladeGrad)" opacity="0.9"/>
                <path d="M0,0 L48,-28 A60,60 0 0,1 56,6 Z" fill="url(#bladeGrad)" opacity="0.9"/>
                <path d="M0,0 L50,24 A60,60 0 0,1 24,52 Z" fill="url(#bladeGrad)" opacity="0.9"/>
                <path d="M0,0 L-24,52 A60,60 0 0,1 -54,24 Z" fill="url(#bladeGrad)" opacity="0.9"/>
                <path d="M0,0 L-56,-4 A60,60 0 0,1 -38,-42 Z" fill="url(#bladeGrad)" opacity="0.9"/>
                <circle r="20" fill="url(#hubGrad)" stroke="rgba(255,255,255,0.25)" stroke-width="1.5"/>
                <circle r="9" fill="${d.glow}" opacity="0.9"/>
            </g>`;
        });
    }

    const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 280" width="100%" height="100%">
        <defs>
            <linearGradient id="bgGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#191232"/>
                <stop offset="100%" stop-color="#0f0b20"/>
            </linearGradient>
            <linearGradient id="shroudGrad" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%" stop-color="#282046"/>
                <stop offset="50%" stop-color="#1b1535"/>
                <stop offset="100%" stop-color="#140f29"/>
            </linearGradient>
            <linearGradient id="bladeGrad" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%" stop-color="#3c3260"/>
                <stop offset="100%" stop-color="#16122a"/>
            </linearGradient>
            <linearGradient id="hubGrad" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%" stop-color="#4a3f78"/>
                <stop offset="100%" stop-color="#1f183c"/>
            </linearGradient>
            <linearGradient id="accentLine" x1="0" y1="0" x2="1" y2="0">
                <stop offset="0%" stop-color="${d.glow}"/>
                <stop offset="50%" stop-color="${d.secGlow}"/>
                <stop offset="100%" stop-color="${d.glow}"/>
            </linearGradient>
            <filter id="glowFilter" x="-20%" y="-20%" width="140%" height="140%">
                <feGaussianBlur stdDeviation="4" result="blur"/>
                <feComposite in="SourceGraphic" in2="blur" operator="over"/>
            </filter>
        </defs>
        <rect width="600" height="280" rx="14" fill="url(#bgGrad)"/>
        <path d="M40,46 L560,46 M40,240 L560,240" stroke="rgba(255,255,255,0.04)" stroke-width="1"/>
        <path d="M100,20 L100,260 M500,20 L500,260" stroke="rgba(255,255,255,0.03)" stroke-width="1"/>
        <rect x="40" y="42" width="520" height="195" rx="16" fill="url(#shroudGrad)" stroke="rgba(255,255,255,0.12)" stroke-width="1.5"/>
        <polygon points="52,42 548,42 540,52 60,52" fill="rgba(255,255,255,0.06)"/>
        <rect x="65" y="56" width="470" height="4" rx="2" fill="url(#accentLine)" filter="url(#glowFilter)"/>
        <text x="65" y="74" fill="${d.glow}" font-family="'Inter', sans-serif" font-weight="900" font-size="11" letter-spacing="3" opacity="0.95">${brandText}</text>
        <text x="535" y="74" fill="#cbd5e1" font-family="'Inter', sans-serif" font-weight="700" font-size="9" letter-spacing="1.5" text-anchor="end" opacity="0.6">${d.series}</text>
        ${fansSvg}
        <rect x="65" y="210" width="470" height="20" rx="6" fill="rgba(0,0,0,0.4)" stroke="rgba(255,255,255,0.06)" stroke-width="1"/>
        <text x="76" y="224" fill="#ffffff" font-family="'Inter', sans-serif" font-weight="800" font-size="11" letter-spacing="1">${d.name}</text>
        <text x="525" y="224" fill="${d.secGlow}" font-family="'Inter', sans-serif" font-weight="700" font-size="9" letter-spacing="1" text-anchor="end">${d.vram}</text>
    </svg>`;

    return 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svg);
}

function handleImgError(img, key) {
    img.onerror = null;
    if (key && typeof getGpuSvg === 'function') {
        img.src = getGpuSvg(key);
    } else {
        img.src = 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=600&q=80';
    }
}

// ==========================================================================
// 2. GPU Hardware Catalog (10 Curated Models)
// ==========================================================================
const gpuCatalog = {
    rtx5090: {
        name: 'GeForce RTX 5090 32GB',
        price: 1999,
        badge: 'Flagship',
        badgeClass: 'flagship',
        photo: 'https://images.unsplash.com/photo-1591488320449-011701bb6704?auto=format&fit=crop&w=800&q=80',
        desc: 'The pinnacle of neural rendering and extreme compute. Built on next-generation Blackwell architecture with 32GB GDDR7, engineered for cinematic ray tracing and advanced generative AI models.',
        arch: 'NVIDIA Blackwell',
        memory: '32GB GDDR7 (512-bit)',
        boost: '2,900 MHz Boost',
        cores: '24,576 CUDA Cores',
        power: '500W Total Board Power',
        interface: 'PCIe 5.0 x16'
    },
    rtx5080: {
        name: 'GeForce RTX 5080 16GB',
        price: 1199,
        badge: 'Latest Gen',
        badgeClass: 'latest',
        photo: 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80',
        desc: 'High-octane enthusiast performance engineered for flawless 4K high-FPS gameplay and intense creative rendering suites with next-gen GDDR7 bandwidth.',
        arch: 'NVIDIA Blackwell',
        memory: '16GB GDDR7 (256-bit)',
        boost: '2,800 MHz Boost',
        cores: '10,752 CUDA Cores',
        power: '360W Total Board Power',
        interface: 'PCIe 5.0 x16'
    },
    rtx5070ti: {
        name: 'GeForce RTX 5070 Ti 16GB',
        price: 799,
        badge: 'Next-Gen Ti',
        badgeClass: 'latest',
        photo: 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=800&q=80',
        desc: 'High-framerate 1440p and 4K powerhouse featuring Blackwell compute clusters, GDDR7 memory, and advanced DLSS 4 neural frame generation.',
        arch: 'NVIDIA Blackwell',
        memory: '16GB GDDR7 (256-bit)',
        boost: '2,750 MHz Boost',
        cores: '8,960 CUDA Cores',
        power: '285W Total Board Power',
        interface: 'PCIe 5.0 x16'
    },
    rtx5070: {
        name: 'GeForce RTX 5070 12GB',
        price: 599,
        badge: 'Next-Gen Value',
        badgeClass: 'value',
        photo: 'https://images.unsplash.com/photo-1555680202-c86f0e12f086?auto=format&fit=crop&w=800&q=80',
        desc: 'Next-generation Blackwell accessibility with exceptional power efficiency, advanced ray tracing cores, and dual-fan stealth acoustics.',
        arch: 'NVIDIA Blackwell',
        memory: '12GB GDDR7 (192-bit)',
        boost: '2,670 MHz Boost',
        cores: '6,400 CUDA Cores',
        power: '220W Total Board Power',
        interface: 'PCIe 5.0 x16'
    },
    rtx4070s: {
        name: 'GeForce RTX 4070 SUPER 12GB',
        price: 599,
        badge: 'Most Popular 1440p',
        badgeClass: 'popular',
        photo: 'https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea?auto=format&fit=crop&w=800&q=80',
        desc: 'The undisputed sweet-spot champion for 1440p gaming. Boosted shader count and ultra-efficient Ada Lovelace architecture loved by millions.',
        arch: 'NVIDIA Ada Lovelace',
        memory: '12GB GDDR6X (192-bit)',
        boost: '2,475 MHz Boost',
        cores: '7,168 CUDA Cores',
        power: '220W Total Board Power',
        interface: 'PCIe 4.0 x16'
    },
    rtx4060: {
        name: 'GeForce RTX 4060 8GB',
        price: 299,
        badge: '#1 Global Best Seller',
        badgeClass: 'popular',
        photo: 'https://images.unsplash.com/photo-1544652478-6653e09f18a2?auto=format&fit=crop&w=800&q=80',
        desc: 'The world\'s #1 most popular GPU on Steam Hardware Survey. Ultra-low 115W power draw, DLSS 3 frame generation, and stellar 1080p/1440p gaming.',
        arch: 'NVIDIA Ada Lovelace',
        memory: '8GB GDDR6 (128-bit)',
        boost: '2,460 MHz Boost',
        cores: '3,072 CUDA Cores',
        power: '115W Total Board Power',
        interface: 'PCIe 4.0 x8'
    },
    rtx3060: {
        name: 'GeForce RTX 3060 12GB',
        price: 279,
        badge: 'The People\'s GPU',
        badgeClass: 'popular',
        photo: 'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?auto=format&fit=crop&w=800&q=80',
        desc: 'The legendary budget champion. Generous 12GB VRAM buffer and rock-solid Ampere reliability, remaining the top choice for casual and esports gamers.',
        arch: 'NVIDIA Ampere',
        memory: '12GB GDDR6 (192-bit)',
        boost: '1,777 MHz Boost',
        cores: '3,584 CUDA Cores',
        power: '170W Total Board Power',
        interface: 'PCIe 4.0 x16'
    },
    rx7900xtx: {
        name: 'Radeon RX 7900 XTX 24GB',
        price: 999,
        badge: 'Team Red Flagship',
        badgeClass: 'red-team',
        photo: 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?auto=format&fit=crop&w=800&q=80',
        desc: 'Chiplet architectural breakthrough with 24GB GDDR6 memory and advanced AMD Radiance Display Engine for uncompromised rasterization performance.',
        arch: 'AMD RDNA 3',
        memory: '24GB GDDR6 (384-bit)',
        boost: '2,500 MHz Boost',
        cores: '6,144 Stream Processors',
        power: '355W Total Board Power',
        interface: 'PCIe 4.0 x16'
    },
    rx7800xt: {
        name: 'Radeon RX 7800 XT 16GB',
        price: 499,
        badge: 'AMD Best Value',
        badgeClass: 'red-team',
        photo: 'https://images.unsplash.com/photo-1526738549149-8e07eca6c147?auto=format&fit=crop&w=800&q=80',
        desc: 'AMD\'s top-selling graphics card offering tremendous price-to-performance, 16GB VRAM buffer, and native high refresh-rate 1440p gaming.',
        arch: 'AMD RDNA 3',
        memory: '16GB GDDR6 (256-bit)',
        boost: '2,430 MHz Boost',
        cores: '3,840 Stream Processors',
        power: '263W Total Board Power',
        interface: 'PCIe 4.0 x16'
    },
    rtx4090: {
        name: 'GeForce RTX 4090 24GB',
        price: 1599,
        badge: '24GB Titan',
        badgeClass: 'value',
        photo: 'https://images.unsplash.com/photo-1591488320449-011701bb6704?auto=format&fit=crop&w=800&q=80',
        desc: 'The proven powerhouse of modern workstations. Huge 24GB G6X memory pool effortlessly handles Blender, Unreal Engine 5, and intense 3D simulations.',
        arch: 'NVIDIA Ada Lovelace',
        memory: '24GB GDDR6X (384-bit)',
        boost: '2,520 MHz Boost',
        cores: '16,384 CUDA Cores',
        power: '450W Total Board Power',
        interface: 'PCIe 4.0 x16'
    }
};

// ==========================================================================
// 3. LocalStorage Cross-Page State & Wallet Management
// ==========================================================================
const DEFAULT_WALLET = 999999;
const DEFAULT_CART = { name: 'GeForce RTX 5090 32GB', price: 1999 };

function getWalletBalance() {
    const saved = localStorage.getItem('vibe_wallet_balance');
    return saved !== null ? parseInt(saved, 10) : DEFAULT_WALLET;
}

function setWalletBalance(amount) {
    localStorage.setItem('vibe_wallet_balance', amount.toString());
    updateGlobalWalletDisplay();
}

function getCartItem() {
    const saved = localStorage.getItem('vibe_cart_item');
    if (saved) {
        try {
            return JSON.parse(saved);
        } catch (e) {
            return DEFAULT_CART;
        }
    }
    return DEFAULT_CART;
}

function setCartItem(item) {
    if (item) {
        localStorage.setItem('vibe_cart_item', JSON.stringify(item));
    } else {
        localStorage.removeItem('vibe_cart_item');
    }
}

const formatMoney = (amount) => Number(amount).toLocaleString('en-US');

function updateGlobalWalletDisplay() {
    const balance = getWalletBalance();
    const formatted = formatMoney(balance);
    const navWallet = document.getElementById('walletBalance');
    const footerWallet = document.getElementById('footerWallet');
    if (navWallet) navWallet.innerText = formatted;
    if (footerWallet) footerWallet.innerText = formatted;
}

function resetWallet() {
    setWalletBalance(DEFAULT_WALLET);
    showToast('Wallet Restored', 'Simulated funds replenished to $999,999.');
    if (typeof updatePaymentUI === 'function') {
        updatePaymentUI();
    }
}

// ==========================================================================
// 4. Custom Toast Notification
// ==========================================================================
function showToast(title, message, isError = false) {
    const toast = document.getElementById('customToast');
    if (!toast) return;

    const icon = toast.querySelector('i');
    toast.querySelector('#toastTitle').innerText = title;
    toast.querySelector('#toastMessage').innerText = message;

    if (isError) {
        toast.style.borderLeftColor = '#ff4d4d';
        if (icon) icon.className = 'bi bi-exclamation-triangle-fill fs-3 text-danger';
        toast.querySelector('#toastTitle').className = 'd-block text-danger fw-bold';
    } else {
        toast.style.borderLeftColor = 'var(--accent-primary)';
        if (icon) icon.className = 'bi bi-check-circle-fill fs-3 text-accent';
        toast.querySelector('#toastTitle').className = 'd-block text-accent fw-bold';
    }

    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3200);
}

// ==========================================================================
// 5. Store & Allocation Workflows
// ==========================================================================
function checkoutItem(name, price) {
    setCartItem({ name, price });
    showToast('Hardware Selected', `${name} ($${formatMoney(price)}) added to allocation.`);
    setTimeout(() => {
        window.location.href = 'payment.html';
    }, 400);
}

let quickViewModalInstance = null;
let currentModalGpuKey = 'rtx5090';

function openQuickView(gpuKey) {
    const gpu = gpuCatalog[gpuKey];
    if (!gpu) return;
    currentModalGpuKey = gpuKey;

    const nameEl = document.getElementById('modalName');
    const priceEl = document.getElementById('modalPrice');
    const descEl = document.getElementById('modalDesc');
    const archEl = document.getElementById('modalArch');
    const memoryEl = document.getElementById('modalMemory');
    const boostEl = document.getElementById('modalBoost');
    const coresEl = document.getElementById('modalCores');
    const powerEl = document.getElementById('modalPower');
    const interfaceEl = document.getElementById('modalInterface');

    if (nameEl) nameEl.innerText = gpu.name;
    if (priceEl) priceEl.innerText = '$' + formatMoney(gpu.price);
    if (descEl) descEl.innerText = gpu.desc;
    if (archEl) archEl.innerText = gpu.arch;
    if (memoryEl) memoryEl.innerText = gpu.memory;
    if (boostEl) boostEl.innerText = gpu.boost;
    if (coresEl) coresEl.innerText = gpu.cores;
    if (powerEl) powerEl.innerText = gpu.power;
    if (interfaceEl) interfaceEl.innerText = gpu.interface;

    const badgeEl = document.getElementById('modalBadge');
    if (badgeEl) {
        badgeEl.className = `badge-status ${gpu.badgeClass} position-static py-1 px-3`;
        badgeEl.innerText = gpu.badge;
    }

    const modalImg = document.getElementById('modalImg');
    if (modalImg) {
        modalImg.src = isStudioArtMode ? (gpu.photo || getGpuSvg(gpuKey)) : getGpuSvg(gpuKey);
        modalImg.onerror = () => handleImgError(modalImg, gpuKey);
    }

    const btnOrder = document.getElementById('modalBtnOrder');
    if (btnOrder) {
        btnOrder.onclick = () => {
            if (quickViewModalInstance) quickViewModalInstance.hide();
            checkoutItem(gpu.name, gpu.price);
        };
    }

    const modalEl = document.getElementById('quickViewModal');
    if (modalEl && typeof bootstrap !== 'undefined') {
        quickViewModalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        quickViewModalInstance.show();
    }
}

// Store Filters
function filterStore(category, btnElement) {
    document.querySelectorAll('.filter-pill').forEach(btn => btn.classList.remove('active'));
    if (btnElement) btnElement.classList.add('active');

    const cards = document.querySelectorAll('.gpu-card-col');
    cards.forEach(card => {
        const brand = card.getAttribute('data-brand');
        const series = card.getAttribute('data-series');
        const popular = card.getAttribute('data-popular') === 'true';
        const vram = parseInt(card.getAttribute('data-vram'), 10);

        let show = false;
        if (category === 'all') {
            show = true;
        } else if (category === '50series' && series === '50') {
            show = true;
        } else if (category === 'popular' && popular) {
            show = true;
        } else if (category === 'nvidia' && brand === 'nvidia') {
            show = true;
        } else if (category === 'amd' && brand === 'amd') {
            show = true;
        } else if (category === 'enthusiast' && vram >= 24) {
            show = true;
        }

        if (show) {
            card.style.display = 'block';
            card.style.animation = 'fadeIn 0.3s ease forwards';
        } else {
            card.style.display = 'none';
        }
    });
}

// Visual Art Switcher
let isStudioArtMode = false;
function toggleArtMode() {
    isStudioArtMode = !isStudioArtMode;
    const btn = document.getElementById('btnArtToggle');
    const images = document.querySelectorAll('.gpu-display-img');

    if (isStudioArtMode) {
        if (btn) {
            btn.innerHTML = '<i class="bi bi-cpu me-1"></i> Switch to 3D Vector Art';
            btn.classList.replace('btn-outline-custom', 'btn-vibe');
        }
        images.forEach(img => {
            const key = img.getAttribute('data-key');
            if (gpuCatalog[key] && gpuCatalog[key].photo) {
                img.src = gpuCatalog[key].photo;
            }
        });
        showToast('Studio Photos Loaded', 'Displaying real hardware studio photography.');
    } else {
        if (btn) {
            btn.innerHTML = '<i class="bi bi-camera me-1"></i> Switch to Studio Photos';
            btn.classList.replace('btn-vibe', 'btn-outline-custom');
        }
        images.forEach(img => {
            const key = img.getAttribute('data-key');
            img.src = getGpuSvg(key);
        });
        showToast('3D Vector Art Loaded', 'Displaying ultra-crisp GPU vector blueprint renders.');
    }
}

// Initialize GPU SVG Images
function initGpuImages() {
    document.querySelectorAll('.gpu-display-img').forEach(img => {
        const key = img.getAttribute('data-key');
        if (key && typeof getGpuSvg === 'function') {
            img.src = getGpuSvg(key);
        }
    });
}

// ==========================================================================
// 6. Benchmark Telemetry Mode Switcher (Promotions Page)
// ==========================================================================
let isRayTracingMode = false;
function toggleBenchmarkMode() {
    isRayTracingMode = !isRayTracingMode;
    const btn = document.getElementById('btnBenchmarkToggle');
    const perfRows = document.querySelectorAll('#promotions .perf-row, .perf-container .perf-row');

    if (isRayTracingMode) {
        if (btn) {
            btn.innerHTML = '<i class="bi bi-bar-chart-fill me-2"></i> Switch to TimeSpy Raster Mode';
            btn.classList.replace('btn-outline-custom', 'btn-vibe');
        }

        const rtData = [
            { name: 'NVIDIA RTX 5090 (32GB GDDR7)', sub: '142 FPS (DLSS 4 Multi-Frame + Path Tracing)', width: '100%' },
            { name: 'NVIDIA RTX 4090 (24GB G6X)', sub: '96 FPS (DLSS 3.5 Ray Reconstruction)', width: '68%' },
            { name: 'AMD Radeon RX 7900 XTX (24GB)', sub: '64 FPS (FSR 3.1 + Hardware RT)', width: '45%' },
            { name: 'NVIDIA RTX 4080 SUPER (16GB)', sub: '82 FPS (DLSS 3.5 Frame Gen)', width: '58%' },
            { name: 'NVIDIA RTX 4070 Ti (12GB)', sub: '60 FPS (DLSS 3 Frame Gen)', width: '42%' }
        ];

        perfRows.forEach((row, idx) => {
            if (rtData[idx]) {
                const label1 = row.querySelector('.perf-label span:first-child');
                const label2 = row.querySelector('.perf-label span:last-child');
                const bar = row.querySelector('.progress-bar');
                if (label1) label1.innerText = rtData[idx].name;
                if (label2) label2.innerText = rtData[idx].sub;
                if (bar) bar.style.width = rtData[idx].width;
            }
        });
        showToast('Telemetry Updated', 'Switched to 4K Cyberpunk Path Tracing benchmarks.');
    } else {
        if (btn) {
            btn.innerHTML = '<i class="bi bi-sliders me-2"></i> Switch to Ray Tracing Mode';
            btn.classList.replace('btn-vibe', 'btn-outline-custom');
        }

        const rasterData = [
            { name: 'NVIDIA RTX 5090 (32GB GDDR7)', sub: '125% Relative Performance / 45,000+ TimeSpy', width: '100%' },
            { name: 'NVIDIA RTX 4090 (24GB G6X)', sub: '100% Relative Performance / 35,000+ TimeSpy', width: '80%' },
            { name: 'AMD Radeon RX 7900 XTX (24GB)', sub: '88% Relative Performance / 29,500+ TimeSpy', width: '88%' },
            { name: 'NVIDIA RTX 4080 SUPER (16GB)', sub: '82% Relative Performance / 28,000+ TimeSpy', width: '82%' },
            { name: 'NVIDIA RTX 4070 Ti (12GB)', sub: '65% Relative Performance / 22,500+ TimeSpy', width: '65%' }
        ];

        perfRows.forEach((row, idx) => {
            if (rasterData[idx]) {
                const label1 = row.querySelector('.perf-label span:first-child');
                const label2 = row.querySelector('.perf-label span:last-child');
                const bar = row.querySelector('.progress-bar');
                if (label1) label1.innerText = rasterData[idx].name;
                if (label2) label2.innerText = rasterData[idx].sub;
                if (bar) bar.style.width = rasterData[idx].width;
            }
        });
        showToast('Telemetry Updated', 'Switched to 3DMark TimeSpy raster benchmarks.');
    }
}

// ==========================================================================
// 7. Payment Simulation (Payment Page)
// ==========================================================================
function updatePaymentUI() {
    const emptyEl = document.getElementById('cartEmpty');
    const contentEl = document.getElementById('cartContent');
    const currentCartItem = getCartItem();
    const currentWalletBalance = getWalletBalance();

    if (!currentCartItem) {
        if (emptyEl) emptyEl.style.display = 'block';
        if (contentEl) contentEl.style.display = 'none';
        return;
    }

    if (emptyEl) emptyEl.style.display = 'none';
    if (contentEl) contentEl.style.display = 'block';

    const nameEl = document.getElementById('summaryItemName');
    const priceEl = document.getElementById('summaryItemPrice');
    const subtotalEl = document.getElementById('summarySubtotal');
    const totalEl = document.getElementById('summaryTotal');
    const walletDisplayEl = document.getElementById('summaryWalletDisplay');
    const remainingEl = document.getElementById('summaryWalletRemaining');

    if (nameEl) nameEl.innerText = currentCartItem.name;
    if (priceEl) priceEl.innerText = '$' + formatMoney(currentCartItem.price);
    if (subtotalEl) subtotalEl.innerText = '$' + formatMoney(currentCartItem.price);
    if (totalEl) totalEl.innerText = '$' + formatMoney(currentCartItem.price);
    if (walletDisplayEl) walletDisplayEl.innerText = '$' + formatMoney(currentWalletBalance);

    const remaining = currentWalletBalance - currentCartItem.price;
    if (remainingEl) {
        remainingEl.innerText = '$' + formatMoney(remaining);
        if (remaining < 0) {
            remainingEl.className = 'text-danger small fw-bold';
        } else {
            remainingEl.className = 'text-light small fw-bold';
        }
    }
}

function processPayment(e) {
    e.preventDefault();
    const currentCartItem = getCartItem();
    const currentWalletBalance = getWalletBalance();

    if (!currentCartItem) {
        showToast('Allocation Error', 'No hardware selected. Please return to the Store.', true);
        return;
    }

    if (currentWalletBalance < currentCartItem.price) {
        showToast('Insufficient Balance', 'Not enough App Pocket Money remaining.', true);
        return;
    }

    const btn = document.getElementById('btnSubmitOrder');
    const originalText = btn ? btn.innerHTML : '';
    if (btn) {
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Encrypting & Finalizing Allocation...';
        btn.disabled = true;
    }

    setTimeout(() => {
        const newBalance = currentWalletBalance - currentCartItem.price;
        setWalletBalance(newBalance);
        showToast('Allocation Secured!', `Payment of $${formatMoney(currentCartItem.price)} confirmed. Order is dispatched.`);

        e.target.reset();
        setCartItem(null);
        updatePaymentUI();

        if (btn) {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }, 1200);
}

// ==========================================================================
// 8. Reviews System (Reviews Page)
// ==========================================================================
function getPersistedReviews() {
    const saved = localStorage.getItem('vibe_reviews');
    if (saved) {
        try {
            return JSON.parse(saved);
        } catch (e) {
            return [];
        }
    }
    return [];
}

function savePersistedReview(review) {
    const reviews = getPersistedReviews();
    reviews.unshift(review);
    localStorage.setItem('vibe_reviews', JSON.stringify(reviews));
}

function renderReviewCard(rev) {
    let starsHtml = '';
    for (let i = 0; i < 5; i++) {
        if (i < rev.rating) starsHtml += '<i class="bi bi-star-fill text-warning me-1"></i>';
        else starsHtml += '<i class="bi bi-star text-secondary me-1"></i>';
    }

    return `
        <div class="vibe-card p-4 border-0" style="animation: fadeIn 0.4s ease;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(140, 82, 255, 0.2) !important; border: 1px solid rgba(140, 82, 255, 0.4);">
                        <i class="bi bi-person-fill fs-3 text-accent"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 text-white fw-bold">${rev.name}</h5>
                        <small class="text-accent fw-500">Verified Owner - ${rev.product}</small>
                    </div>
                </div>
                <div>${starsHtml}</div>
            </div>
            <p class="text-muted fst-italic mb-0 lh-lg">"${rev.text}"</p>
            <small class="text-secondary mt-3 d-block fw-bold">Posted: ${rev.date}</small>
        </div>
    `;
}

function loadPersistedReviews() {
    const container = document.getElementById('reviewsContainer');
    if (!container) return;

    const savedReviews = getPersistedReviews();
    savedReviews.forEach(rev => {
        container.insertAdjacentHTML('afterbegin', renderReviewCard(rev));
    });
}

function submitReview(e) {
    e.preventDefault();
    const name = document.getElementById('revName').value;
    const product = document.getElementById('revProduct').value;
    const rating = parseInt(document.getElementById('revRating').value, 10);
    const text = document.getElementById('revText').value;
    const today = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });

    const reviewObj = { name, product, rating, text, date: today };
    savePersistedReview(reviewObj);

    const container = document.getElementById('reviewsContainer');
    if (container) {
        container.insertAdjacentHTML('afterbegin', renderReviewCard(reviewObj));
    }

    showToast('Testimonial Published', 'Thank you for sharing your hardware experience!');
    e.target.reset();
}

// ==========================================================================
// 9. Application Initialization
// ==========================================================================
function initApp() {
    updateGlobalWalletDisplay();
    initGpuImages();

    if (document.getElementById('checkoutForm')) {
        updatePaymentUI();

        // Payment type toggle listener
        document.querySelectorAll('input[name="paymentType"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                const cardFields = document.getElementById('cardFields');
                if (cardFields) {
                    cardFields.style.display = e.target.value === 'card' ? 'flex' : 'none';
                }
            });
        });
    }

    if (document.getElementById('reviewsContainer')) {
        loadPersistedReviews();
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApp);
} else {
    initApp();
}
