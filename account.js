// account.js — Sistema de sesión, menú de usuario y Control de Edad

function renderNavAccount() {
  const accountItem = document.getElementById('nav-account-item');
  if (!accountItem) return;

  const currentUser = JSON.parse(localStorage.getItem('currentUser'));

  if (!currentUser) {
    accountItem.innerHTML = `<a href="login.html" class="nav-login-btn">Acceder</a>`;
    return;
  }

  const initial = currentUser.name.charAt(0).toUpperCase();
  
  // Comprobamos si es admin para inyectar el enlace
  const adminLink = currentUser.rol === 'admin' 
    ? `<a class="account-dropdown-item" href="admin.html" style="color:#d4af37 !important;">⚙️ Panel Admin</a>` 
    : '';

  accountItem.innerHTML = `
    <div class="account-menu-wrapper" id="account-wrapper">
      <button class="account-trigger" onclick="toggleAccountDropdown(event)" aria-haspopup="true">
        <span class="account-avatar">${initial}</span>
        <span class="account-name">${currentUser.name.split(' ')[0]}</span>
        <span class="account-chevron">▾</span>
      </button>
      <div class="account-dropdown" id="account-dropdown">
        <div class="account-dropdown-header">
          <div class="account-dropdown-avatar">${initial}</div>
          <div>
            <div class="account-dropdown-name">${currentUser.name}</div>
            <div class="account-dropdown-email">${currentUser.email}</div>
          </div>
        </div>
        <hr class="account-dropdown-divider">
        ${adminLink}
        <a class="account-dropdown-item" href="perfil.html">👤 Mi Perfil</a>
        <a class="account-dropdown-item" href="#" onclick="handleLogout(event)">🚪 Cerrar sesión</a>
      </div>
    </div>
  `;
}

function toggleAccountDropdown(e) {
  e.stopPropagation();
  const dropdown = document.getElementById('account-dropdown');
  const wrapper = document.getElementById('account-wrapper');
  if (!dropdown) return;
  const isOpen = dropdown.classList.contains('open');
  dropdown.classList.toggle('open', !isOpen);
  wrapper.classList.toggle('open', !isOpen);
}

function handleLogout(e) {
  e.preventDefault();
  localStorage.removeItem('currentUser');
  window.location.href = 'index.html';
}

document.addEventListener('click', () => {
  const dropdown = document.getElementById('account-dropdown');
  const wrapper = document.getElementById('account-wrapper');
  if (dropdown) dropdown.classList.remove('open');
  if (wrapper) wrapper.classList.remove('open');
});

// MODAL DE EDAD GLOBAL
function renderAgeModal() {
  if (localStorage.getItem('ageVerified') === 'true') return;

  const modalHTML = `
    <div id="ageModal" class="age-modal-overlay">
      <div class="age-modal-box">
        <p>Esta página puede mostrar productos no aptos para todas las edades, ¿tienes 18 años o más?</p>
        <div>
          <button class="btn yes" onclick="verifyAge(true)">Sí</button>
          <button class="btn no" style="background:#555; color:#fff;" onclick="verifyAge(false)">No</button>
        </div>
      </div>
    </div>
  `;
  document.body.insertAdjacentHTML('beforeend', modalHTML);
}

window.verifyAge = function(isAdult) {
  if (isAdult) {
    localStorage.setItem('ageVerified', 'true');
    const modal = document.getElementById('ageModal');
    if(modal) modal.remove();
  } else {
    alert('No puedes acceder a este sitio. Serás redirigido.');
    window.location.href = 'https://www.google.com';
  }
};

document.addEventListener('DOMContentLoaded', () => {
  renderNavAccount();
  renderAgeModal();
});