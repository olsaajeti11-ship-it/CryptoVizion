const cryptoData = [
    { name: "Bitcoin", symbol: "BTC", price: 40000, change: 0, volume: 5000000 },
    { name: "Ethereum", symbol: "ETH", price: 2500, change: 0, volume: 3000000 },
    { name: "Solana", symbol: "SOL", price: 150, change: 0, volume: 1000000 },
    { name: "Cardano", symbol: "ADA", price: 1.25, change: 0, volume: 800000 },
    { name: "Ripple", symbol: "XRP", price: 0.85, change: 0, volume: 600000 },
    { name: "Polkadot", symbol: "DOT", price: 35, change: 0, volume: 400000 }
];

function updateTable() {
    const tbody = document.getElementById('cryptoTableBody');
    tbody.innerHTML = '';
    cryptoData.forEach(crypto => {
        const randomChange = (Math.random() * 4 - 2).toFixed(2);
        crypto.change = parseFloat(randomChange);
        crypto.price = (crypto.price * (1 + crypto.change / 100)).toFixed(2);
        const row = document.createElement('tr');
        const nameCell = document.createElement('td');
        nameCell.innerHTML = `<div class="crypto-name"><strong>${crypto.name}</strong><span class="crypto-symbol">${crypto.symbol}</span></div>`;
        const priceCell = document.createElement('td');
        priceCell.textContent = "$" + parseFloat(crypto.price).toLocaleString();
        const changeCell = document.createElement('td');
        changeCell.textContent = crypto.change + "%";
        changeCell.className = crypto.change > 0 ? 'positive' : crypto.change < 0 ? 'negative' : '';
        changeCell.style.fontWeight = 'bold';
        const volumeCell = document.createElement('td');
        volumeCell.textContent = "$" + crypto.volume.toLocaleString();
        const trendCell = document.createElement('td');
        trendCell.textContent = crypto.change > 0 ? '📈' : crypto.change < 0 ? '📉' : '➡️';
        trendCell.style.fontSize = '20px';
        row.appendChild(nameCell);
        row.appendChild(priceCell);
        row.appendChild(changeCell);
        row.appendChild(volumeCell);
        row.appendChild(trendCell);
        tbody.appendChild(row);
    });
}

updateTable();
setInterval(updateTable, 3000);

document.getElementById('exploreBtn').addEventListener('click', () => {
    const user = localStorage.getItem("cryptoUser");
    if(!user){
        alert("Ju lutem kyçuni fillimisht 🔒");
        loginModal.style.display = "flex";
        return;
    }
    document.getElementById("market").scrollIntoView({behavior:"smooth"});
});

document.querySelectorAll('nav a').forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        const targetId = link.getAttribute('href');
        document.querySelector(targetId).scrollIntoView({ behavior: 'smooth' });
    });
});

const scrollTopBtn = document.getElementById('scrollTop');
window.addEventListener('scroll', () => {
    if (window.pageYOffset > 300) {
        scrollTopBtn.classList.add('visible');
    } else {
        scrollTopBtn.classList.remove('visible');
    }
});
scrollTopBtn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

document.getElementById('contactForm').addEventListener('submit', (e) => {
    e.preventDefault();
    alert('Faleminderit për mesazhin tuaj! Do t\'ju kontaktojmë së shpejti.');
    e.target.reset();
});

document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    });
});

const animateStats = () => {
    const statsSection = document.querySelector('.stats');
    const statsPosition = statsSection.getBoundingClientRect().top;
    const screenPosition = window.innerHeight;
    if(statsPosition < screenPosition){
        statsSection.style.opacity = '1';
        statsSection.style.transform = 'translateY(0)';
    }
};
window.addEventListener('scroll', animateStats);

// ================= LOGIN SYSTEM =================

const loginBtn = document.getElementById("loginBtn");
const loginModal = document.getElementById("loginModal");
const closeLogin = document.getElementById("closeLogin");

const loginForm = document.getElementById("loginForm");
const loginEmail = document.getElementById("loginEmail");
const loginPassword = document.getElementById("loginPassword");
const loginError = document.getElementById("loginError");


// Kontrollo nese user eshte loguar
function checkLogin(){

    const user = localStorage.getItem("cryptoUser");

    if(user){

        loginModal.style.display = "none";
        document.body.style.overflow = "auto";

        loginBtn.textContent =
            "Logout (" + user.split("@")[0] + ")";

    }else{

        loginModal.style.display = "flex";
        document.body.style.overflow = "hidden";

        loginBtn.textContent = "Login";

    }

}


// Kur hapet faqja
window.addEventListener("load", () => {

    checkLogin();

});


// Klik Login / Logout
loginBtn.addEventListener("click", () => {

    const user = localStorage.getItem("cryptoUser");

    if(user){

        if(confirm("Dëshiron të dalësh?")){

            localStorage.removeItem("cryptoUser");

            checkLogin();

        }

    }else{

        loginModal.style.display = "flex";
        document.body.style.overflow = "hidden";

    }

});


// Mbylle modalin
closeLogin.addEventListener("click", () => {

    loginModal.style.display = "none";

    document.body.style.overflow = "auto";

});


// Login submit
loginForm.addEventListener("submit", e => {

    e.preventDefault();

    const email = loginEmail.value.trim();
    const pass = loginPassword.value.trim();


    if(!email.includes("@")){

        loginError.textContent =
            "Email jo valid!";

        return;

    }


    if(pass.length < 6){

        loginError.textContent =
            "Password minimum 6 karaktere!";

        return;

    }


    localStorage.setItem("cryptoUser", email);

    loginError.textContent = "";

    loginForm.reset();

    checkLogin();

});


