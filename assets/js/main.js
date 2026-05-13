$(document).ready(function() {
  // Add to Cart
  $('.add-to-cart, .quick-add-cart').click(function(e) {
      e.preventDefault();
      var $btn = $(this);
      var productId = $btn.data('id');
      var quantity = $('#qty').length ? $('#qty').val() : 1;
      var size = $('.size-btn.active').length ? $('.size-btn.active').data('size') : null;
      var color = $('.color-btn.active').length ? $('.color-btn.active').data('color') : null;
      
      $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
      $.ajax({
          url: 'includes/cart.php',
          method: 'POST',
          data: { action: 'add', product_id: productId, quantity: quantity, size: size, color: color },
          success: function(res) {
              var data = JSON.parse(res);
              if(data.success) {
                  $('#cart-count').text(data.cart_count);
                  showToast('Added to cart!', 'success');
              } else {
                  showToast(data.message, 'error');
              }
              $btn.html('<i class="fas fa-cart-plus"></i> Add to Cart').prop('disabled', false);
          },
          error: function() {
              showToast('Error adding to cart', 'error');
              $btn.html('<i class="fas fa-cart-plus"></i> Add to Cart').prop('disabled', false);
          }
      });
  });
  
  // Add to Wishlist
  $('.add-to-wishlist').click(function(e) {
      e.preventDefault();
      var $btn = $(this);
      var productId = $btn.data('id');
      $.ajax({
          url: 'wishlist-ajax.php',
          method: 'POST',
          data: { product_id: productId },
          success: function(res) {
              var data = JSON.parse(res);
              if(data.added) {
                  $btn.html('<i class="fas fa-heart"></i>');
                  showToast('Added to wishlist!', 'success');
              } else {
                  $btn.html('<i class="far fa-heart"></i>');
                  showToast('Removed from wishlist', 'info');
              }
              $('#wishlist-count').text(data.count);
          }
      });
  });
  
  // Remove from Wishlist
  $('.remove-wishlist').click(function(e) {
      e.preventDefault();
      if(confirm('Remove from wishlist?')) {
          $.ajax({
              url: 'wishlist-ajax.php',
              method: 'POST',
              data: { product_id: $(this).data('id') },
              success: function() { location.reload(); }
          });
      }
  });
  
  // Change Currency
  $('.change-currency').click(function(e) {
      e.preventDefault();
      var currency = $(this).data('currency');
      $.ajax({
          url: 'includes/currency.php',
          method: 'POST',
          data: { change_currency: currency },
          success: function() { location.reload(); }
      });
  });
  
  // Update Cart Quantity
  $('.cart-qty').change(function() {
      var cartId = $(this).data('id');
      var quantity = $(this).val();
      $.ajax({
          url: 'includes/cart.php',
          method: 'POST',
          data: { action: 'update', cart_id: cartId, quantity: quantity },
          success: function() { location.reload(); }
      });
  });
  
  // Remove Cart Item
  $('.remove-item').click(function() {
      if(confirm('Remove item from cart?')) {
          var cartId = $(this).data('id');
          $.ajax({
              url: 'includes/cart.php',
              method: 'POST',
              data: { action: 'remove', cart_id: cartId },
              success: function() { location.reload(); }
          });
      }
  });
  
  // Size Selection
  $('.size-btn').click(function() {
      $('.size-btn').removeClass('active btn-primary').addClass('btn-outline-secondary');
      $(this).removeClass('btn-outline-secondary').addClass('active btn-primary');
  });
  
  // Color Selection
  $('.color-btn').click(function() {
      $('.color-btn').removeClass('active border-primary');
      $(this).addClass('active border-primary');
  });
  
  // Quantity Controls
  $('.quantity-plus').click(function() {
      var input = $('#qty');
      input.val(parseInt(input.val()) + 1);
  });
  
  $('.quantity-minus').click(function() {
      var input = $('#qty');
      if(parseInt(input.val()) > 1) {
          input.val(parseInt(input.val()) - 1);
      }
  });
  
  // Sort Products
  $('#sort-by').change(function() {
      var url = new URL(window.location.href);
      url.searchParams.set('sort', $(this).val());
      window.location.href = url.toString();
  });
  
  // Checkout Form
  $('#checkout-form').submit(function(e) {
      e.preventDefault();
      $('button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop('disabled', true);
      $.ajax({
          url: 'process-order.php',
          method: 'POST',
          data: $(this).serialize(),
          success: function(res) {
              var data = JSON.parse(res);
              if(data.success) {
                  alert('Order placed successfully!\nOrder Number: ' + data.order_number);
                  window.location.href = 'index.php';
              } else {
                  alert('Error: ' + data.message);
                  $('button[type="submit"]').html('Place Order').prop('disabled', false);
              }
          },
          error: function() {
              alert('An error occurred. Please try again.');
              $('button[type="submit"]').html('Place Order').prop('disabled', false);
          }
      });
  });
  
  // Toast Notification
  function showToast(message, type) {
      var bgColor = type === 'success' ? '#28a745' : (type === 'error' ? '#dc3545' : '#17a2b8');
      var icon = type === 'success' ? 'check-circle' : (type === 'error' ? 'exclamation-circle' : 'info-circle');
      
      var toast = $(`
          <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
              <div class="toast show" role="alert">
                  <div class="toast-header" style="background: ${bgColor}; color: white;">
                      <i class="fas fa-${icon} me-2"></i>
                      <strong class="me-auto">Notification</strong>
                      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                  </div>
                  <div class="toast-body">${message}</div>
              </div>
          </div>
      `);
      
      $('body').append(toast);
      setTimeout(function() { toast.remove(); }, 3000);
  }
  
  // Load counts
  $.ajax({
      url: 'includes/cart.php',
      method: 'GET',
      data: { action: 'count' },
      success: function(res) {
          var data = JSON.parse(res);
          $('#cart-count').text(data.count);
      }
  });
  
  $.ajax({
      url: 'wishlist-count.php',
      method: 'GET',
      success: function(res) {
          var data = JSON.parse(res);
          $('#wishlist-count').text(data.count);
      }
  });
});