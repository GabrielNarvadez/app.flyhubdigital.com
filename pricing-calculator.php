<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>Pricing Calculator | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
        .pricing-card {
            border-radius: 13px;
            box-shadow: 0 2px 8px #0001;
            background: #fff;
            margin-bottom: 1.5rem;
            border: 1px solid #e6e9f1;
            min-width: 220px;
        }
        .pricing-card.featured {
            border: 2px solid #266ef2;
        }
        .pricing-price {
            font-size: 2.2rem;
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: .2rem;
        }
        .pricing-currency {
            font-size: 1rem;
            font-weight: 500;
            margin-right: .2rem;
        }
        .pricing-features li {
            font-size: .99rem;
            margin-bottom: .36rem;
        }
        .savings-badge {
            font-size: .87rem;
            background: #eaf8ec;
            color: #13c06d;
            border-radius: 6px;
            padding: .1em .6em;
            font-weight: 500;
            margin-left: .35em;
        }
        .add-on-input {
            width: 70px;
            display: inline-block;
            margin-right: 0.5rem;
        }
        .pricing-addons label {
            font-size: .96rem;
            margin-bottom: .16rem;
        }
        .currency-table th, .currency-table td {
            font-size: 1.09rem;
            vertical-align: middle;
        }
        .custom-dev-form input, .custom-dev-form textarea {
            font-size: .98rem;
        }
    </style>
</head>

<body>
<div class="wrapper">

    <?php include 'layouts/menu.php'; ?>

    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <div class="row mb-3">
                    <div class="col-md-6 d-flex align-items-end gap-3">
                        <h4 class="page-title mb-0">Pricing Calculator</h4>
                    </div>
                    <div class="col-md-6 d-flex justify-content-end align-items-center gap-3">
                        <label class="form-label mb-0 me-2">Currency:</label>
                        <select class="form-select form-select-sm w-auto" id="currencySelect">
                            <option value="PHP" selected>₱ PHP</option>
                            <option value="USD">$ USD</option>
                            <option value="GBP">£ GBP</option>
                            <option value="AUD">A$ AUD</option>
                        </select>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="billingCycleToggle">
                            <label class="form-check-label" for="billingCycleToggle" id="billingCycleLabel">Monthly</label>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-2" id="pricingCards">
                    <!-- Pricing cards populated by JS -->
                </div>

                <div class="row mb-3">
                    <div class="col-lg-7 mb-3">
                        <div class="card p-3 h-100">
                            <h5 class="fw-bold mb-3">Pricing Summary</h5>
                            <div id="pricingSummary"></div>
                            <table class="table table-sm mt-2 currency-table" style="max-width: 480px;">
                                <thead>
                                    <tr>
                                        <th>Currency</th>
                                        <th>Monthly</th>
                                        <th>Yearly</th>
                                    </tr>
                                </thead>
                                <tbody id="allCurrencyTable">
                                    <!-- All prices in different currencies shown here -->
                                </tbody>
                            </table>
                            <button class="btn btn-primary btn-lg mt-3" id="contactSalesBtn">
                                <i class="ri-mail-send-line"></i> Contact Sales
                            </button>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card p-3 h-100">
                            <h5 class="fw-bold mb-3">Request Custom Development</h5>
                            <form class="custom-dev-form" id="customDevForm">
                                <div class="mb-2">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" required name="name">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" required name="email">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Company</label>
                                    <input type="text" class="form-control" name="company">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Project Details / Requirements</label>
                                    <textarea class="form-control" rows="3" name="details" required></textarea>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Desired Timeline</label>
                                    <input type="text" class="form-control" name="timeline" placeholder="e.g. 2 weeks, ASAP">
                                </div>
                                <button class="btn btn-outline-primary mt-2" type="submit">
                                    <i class="ri-send-plane-line"></i> Submit Inquiry
                                </button>
                                <div id="customDevMsg" class="mt-2 text-success" style="display:none">Submitted! Our team will contact you soon.</div>
                            </form>
                        </div>
                    </div>
                </div>

            </div><!-- container-fluid -->
        </div><!-- content -->

        <?php include 'layouts/footer.php'; ?>

    </div>
</div>
<?php include 'layouts/right-sidebar.php'; ?>
<?php include 'layouts/footer-scripts.php'; ?>

<script>
// --- PRICING DATA ---
const exchangeRates = {
    PHP: 1,
    USD: 0.018,
    GBP: 0.013,
    AUD: 0.026
};

const basePrices = {
    Free:    {PHP:0, USD:0, GBP:0, AUD:0},
    Tier1:   {PHP:4990, USD:90, GBP:65, AUD:130},
    Tier2:   {PHP:24990, USD:450, GBP:320, AUD:650},
    Tier3:   {PHP:49990, USD:900, GBP:650, AUD:1300}
};
const yearlyDiscount = 0.16; // 2 months free

const included = {
    Free:   { users: 1,  storage: 0.1, modules: 1 },
    Tier1:  { users: 5,  storage: 5, modules: 3 },
    Tier2:  { users: 20, storage: 30, modules: 5 },
    Tier3:  { users: 60, storage: 200, modules: 10 }
};

const addOnPrices = {
    user:    {PHP:350, USD:6.5, GBP:5, AUD:9},
    storage: {PHP:500, USD:9, GBP:7, AUD:13},
    module:  {PHP:990, USD:18, GBP:13, AUD:25},
    branding:{PHP:2500, USD:45, GBP:32, AUD:65}
};

const tierDetails = {
    Free: {
        title: "Free Forever",
        priceKey: "Free",
        highlights: [
            "1 User",
            "1 Company/Client",
            "1 Core Module",
            "100MB Storage",
            "Basic Mobile Access",
            "Email Support",
            "Great for demo & learning"
        ]
    },
    Tier1: {
        title: "Tier 1 – Solopreneur",
        priceKey: "Tier1",
        highlights: [
            "5 Users Included",
            "3 Companies/Clients",
            "All Core Modules",
            "5GB Storage",
            "1 API Integration",
            "Email & Chat Support",
            "Add-ons available"
        ]
    },
    Tier2: {
        title: "Tier 2 – SME/Startup",
        priceKey: "Tier2",
        highlights: [
            "20 Users Included",
            "30 Companies/Clients",
            "All + 2 Industry Modules",
            "30GB Storage",
            "3 API Integrations",
            "Priority Support",
            "Custom domain/branding (add-on)",
            "Reporting & Analytics"
        ]
    },
    Tier3: {
        title: "Tier 3 – Enterprise",
        priceKey: "Tier3",
        highlights: [
            "60 Users Included",
            "Unlimited Clients",
            "All Modules & Verticals",
            "200GB Storage",
            "Unlimited Integrations",
            "Dedicated Account Manager",
            "Customizations/Discounted Mandays",
            "Branding & White-label Included"
        ]
    },
    Custom: {
        title: "Tier 4 – Custom",
        priceKey: "",
        highlights: [
            "Unlimited Users",
            "Unlimited Modules & Storage",
            "Custom Contracts & Support",
            "Dedicated Account/Team",
            "Custom Integrations",
            "Special compliance/on-premise"
        ]
    }
};

// --- INIT UI ---
let selectedTier = 'Tier1';
let selectedCurrency = 'PHP';
let isYearly = false;
let addons = {user: 0, storage: 0, module: 0, branding: false};

function renderPricingCards() {
    let cardsHtml = '';
    ['Free','Tier1','Tier2','Tier3','Custom'].forEach(tier => {
        let isPaid = tier !== "Free" && tier !== "Custom";
        let isCustom = tier === "Custom";
        let featured = tier === "Tier2";
        let highlights = tierDetails[tier].highlights.map(l => `<li>${l}</li>`).join('');
        let currency = selectedCurrency;
        let base = tierDetails[tier].priceKey ? basePrices[tierDetails[tier].priceKey][currency] : '';
        let displayPrice = "";
        let priceUnit = isYearly && isPaid ? (base * 12 * (1-yearlyDiscount)) : (isPaid ? base : 0);
        let priceUnitMonthly = isPaid ? base : 0;
        let badge = isYearly && isPaid ? `<span class="savings-badge">Save 16%</span>` : '';
        displayPrice = isCustom ? `<span class="pricing-price">Contact Us</span>` :
            `<span class="pricing-currency">${currencySymbol(currency)}</span>
             <span class="pricing-price">${formatPrice(priceUnit, currency)}</span>
             <span class="small">${isYearly ? '/yr' : '/mo'}</span> ${badge}`;
        cardsHtml += `
        <div class="col-md-6 col-lg-2">
            <div class="pricing-card p-3 ${featured ? 'featured':''} text-center h-100 d-flex flex-column justify-content-between ${selectedTier===tier?'border-primary':''}" style="cursor:pointer;" onclick="selectTier('${tier}')">
                <div>
                    <div class="fw-bold fs-5 mb-2">${tierDetails[tier].title}</div>
                    ${displayPrice}
                    <ul class="pricing-features mt-3 mb-3 text-start">${highlights}</ul>
                </div>
                ${isPaid ? `
                <div class="pricing-addons mt-auto">
                  <label class="form-label mb-1 fw-semibold">Add-ons</label>
                  <div class="mb-2">
                    <input type="number" min="0" class="form-control form-control-sm add-on-input" id="addon-user-${tier}" value="${addons.user}" onchange="setAddon('user',this.value)" ${selectedTier!==tier?'disabled':''}>
                    <span class="small">Extra Users</span>
                    <span class="text-muted">(@${currencySymbol(currency)}${addOnPrices.user[currency]}/mo)</span>
                  </div>
                  <div class="mb-2">
                    <input type="number" min="0" class="form-control form-control-sm add-on-input" id="addon-storage-${tier}" value="${addons.storage}" onchange="setAddon('storage',this.value)" ${selectedTier!==tier?'disabled':''}>
                    <span class="small">Extra Storage (per 20GB)</span>
                    <span class="text-muted">(@${currencySymbol(currency)}${addOnPrices.storage[currency]}/mo)</span>
                  </div>
                  <div class="mb-2">
                    <input type="number" min="0" class="form-control form-control-sm add-on-input" id="addon-module-${tier}" value="${addons.module}" onchange="setAddon('module',this.value)" ${selectedTier!==tier?'disabled':''}>
                    <span class="small">Extra Modules</span>
                    <span class="text-muted">(@${currencySymbol(currency)}${addOnPrices.module[currency]}/mo)</span>
                  </div>
                  <div>
                    <input type="checkbox" id="addon-branding-${tier}" class="form-check-input me-1" onchange="setAddon('branding',this.checked)" ${addons.branding?'checked':''} ${selectedTier!==tier?'disabled':''}>
                    <span class="small">Custom Branding/Domain</span>
                    <span class="text-muted">(@${currencySymbol(currency)}${addOnPrices.branding[currency]}/mo)</span>
                  </div>
                </div>` : isCustom ? `<div class="mt-2"><a href="#customDevForm" class="btn btn-outline-secondary btn-sm">Request Quote</a></div>` : ""}
            </div>
        </div>
        `;
    });
    document.getElementById("pricingCards").innerHTML = cardsHtml;
    renderSummary();
}

function currencySymbol(cur) {
    return {PHP:"₱", USD:"$", GBP:"£", AUD:"A$"}[cur]||cur;
}
function formatPrice(price, cur) {
    if(cur==="PHP") return Math.round(price).toLocaleString();
    return price.toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});
}
function setAddon(key,val) {
    if(key==="branding") addons.branding = !!val;
    else addons[key] = Math.max(0, parseInt(val)||0);
    renderSummary();
}
function selectTier(tier) {
    selectedTier = tier;
    if(tier==="Free"||tier==="Custom") addons={user:0,storage:0,module:0,branding:false};
    renderPricingCards();
}
function renderSummary() {
    let cur = selectedCurrency;
    let isPaid = selectedTier!=="Free" && selectedTier!=="Custom";
    let base = isPaid ? basePrices[selectedTier][cur] : 0;
    let summary = "";
    let usersInc = included[selectedTier]?included[selectedTier].users:0;
    let storageInc = included[selectedTier]?included[selectedTier].storage:0;
    let modInc = included[selectedTier]?included[selectedTier].modules:0;
    let extraUsers = Math.max(0, addons.user);
    let extraStorage = Math.max(0, addons.storage);
    let extraModules = Math.max(0, addons.module);
    let branding = addons.branding ? addOnPrices.branding[cur] : 0;

    let addOnTotal = extraUsers*addOnPrices.user[cur] + extraStorage*addOnPrices.storage[cur] + extraModules*addOnPrices.module[cur] + branding;
    let total = isPaid ? (base + addOnTotal) : base;
    let totalYear = isPaid ? (base*12*(1-yearlyDiscount) + addOnTotal*12*(1-yearlyDiscount)) : 0;

    summary += `<div class="mb-1"><strong>Plan:</strong> ${tierDetails[selectedTier].title}</div>`;
    if(isPaid) {
        summary += `<div class="mb-1"><strong>Base Price:</strong> ${currencySymbol(cur)}${formatPrice(base,cur)} /mo (${isYearly? 'yearly with 16% discount' : 'monthly'})</div>`;
        if(extraUsers>0) summary += `<div class="mb-1">+ Extra Users: ${extraUsers} (${currencySymbol(cur)}${formatPrice(extraUsers*addOnPrices.user[cur],cur)}/mo)</div>`;
        if(extraStorage>0) summary += `<div class="mb-1">+ Extra Storage: ${extraStorage*20}GB (${currencySymbol(cur)}${formatPrice(extraStorage*addOnPrices.storage[cur],cur)}/mo)</div>`;
        if(extraModules>0) summary += `<div class="mb-1">+ Extra Modules: ${extraModules} (${currencySymbol(cur)}${formatPrice(extraModules*addOnPrices.module[cur],cur)}/mo)</div>`;
        if(branding>0) summary += `<div class="mb-1">+ Custom Branding/Domain (${currencySymbol(cur)}${formatPrice(branding,cur)}/mo)</div>`;
        summary += `<div class="mb-2"><strong>Total:</strong> ${currencySymbol(cur)}${formatPrice(isYearly?totalYear:total,cur)} /${isYearly?'yr':'mo'}</div>`;
    } else if(selectedTier==="Custom") {
        summary += `<div class="mb-2"><em>Let us design a plan and quote for your business. Submit your requirements on the right.</em></div>`;
    } else {
        summary += `<div class="mb-2">No payment or credit card required.</div>`;
    }
    document.getElementById("pricingSummary").innerHTML = summary;
    renderCurrencyTable(total,totalYear);
}

function renderCurrencyTable(monthly,yearly) {
    let rows = '';
    Object.keys(exchangeRates).forEach(cur=>{
        let b = basePrices[selectedTier][cur]||0;
        let isPaid = selectedTier!=="Free" && selectedTier!=="Custom";
        let eu = Math.max(0, addons.user);
        let es = Math.max(0, addons.storage);
        let em = Math.max(0, addons.module);
        let br = addons.branding ? addOnPrices.branding[cur] : 0;
        let addOnTotal = eu*addOnPrices.user[cur] + es*addOnPrices.storage[cur] + em*addOnPrices.module[cur] + br;
        let t = isPaid ? (b + addOnTotal) : b;
        let ty = isPaid ? (b*12*(1-yearlyDiscount) + addOnTotal*12*(1-yearlyDiscount)) : 0;
        let symbol = currencySymbol(cur);
        rows += `<tr>
            <td><b>${symbol}</b> ${cur}</td>
            <td>${symbol}${formatPrice(t,cur)} /mo</td>
            <td>${symbol}${formatPrice(ty,cur)} /yr</td>
        </tr>`;
    });
    document.getElementById("allCurrencyTable").innerHTML = rows;
}

// --- EVENT HANDLERS ---
document.getElementById('currencySelect').addEventListener('change',e=>{
    selectedCurrency = e.target.value;
    renderPricingCards();
});
document.getElementById('billingCycleToggle').addEventListener('change',e=>{
    isYearly = !!e.target.checked;
    document.getElementById('billingCycleLabel').innerText = isYearly ? 'Yearly' : 'Monthly';
    renderPricingCards();
});
document.getElementById('customDevForm').addEventListener('submit',function(e){
    e.preventDefault();
    document.getElementById('customDevMsg').style.display='block';
    setTimeout(()=>{ document.getElementById('customDevMsg').style.display='none'; }, 3500);
    this.reset();
});
document.getElementById('contactSalesBtn').addEventListener('click',function(e){
    alert("Thank you! A sales specialist will contact you soon.");
});

// --- INIT ---
renderPricingCards();
</script>

</body>
</html>
