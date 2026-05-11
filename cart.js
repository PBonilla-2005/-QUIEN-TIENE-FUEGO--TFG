// cart.js — Sistema de carrito global y notificaciones

function addToCart(button) {
  const card = button.closest('.product-card');
  const product = {
    id: card.dataset.id,
    name: card.dataset.name,
    price: parseFloat(card.dataset.price),
    img: card.dataset.img,
    qty: 1
  };

  let cart = JSON.parse(localStorage.getItem('cart')) || [];
  const existingProductIndex = cart.findIndex(item => item.id === product.id);

  if (existingProductIndex > -1) {
    cart[existingProductIndex].qty += 1;
    showToast('Se ha añadido otra unidad de ' + product.name);
  } else {
    cart.push(product);
    showToast(product.name + ' añadido al carrito');
  }

  localStorage.setItem('cart', JSON.stringify(cart));
  updateCartCount();
}

function updateCartCount() {
  const cart = JSON.parse(localStorage.getItem('cart')) || [];
  const countSpan = document.getElementById('cart-count');
  if (!countSpan) return;
  
  const totalQty = cart.reduce((acc, item) => acc + item.qty, 0);
  
  if(totalQty > 0) {
    countSpan.style.display = 'inline-block';
    countSpan.innerText = totalQty;
  } else {
    countSpan.style.display = 'none';
  }
}

// Sistema de Notificaciones UI (Toast)
function showToast(message) {
  let toast = document.getElementById('toast-container');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'toast-container';
    document.body.appendChild(toast);
  }
  toast.innerText = message;
  toast.classList.add('show');
  
  setTimeout(() => {
    toast.classList.remove('show');
  }, 3000);
}

// Inicializar al cargar
document.addEventListener('DOMContentLoaded', updateCartCount);