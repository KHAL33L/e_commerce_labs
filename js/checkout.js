// js/checkout.js
// Manage the simulated payment modal and checkout process

document.addEventListener('DOMContentLoaded', function() {
    const checkoutForm = document.getElementById('checkoutForm');
    const placeOrderBtn = document.getElementById('placeOrderBtn');
    
    if (checkoutForm && placeOrderBtn) {
        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate form
            if (!validateCheckoutForm()) {
                return;
            }
            
            // Show payment modal
            showPaymentModal();
        });
    }
});

/**
 * Validate checkout form
 */
function validateCheckoutForm() {
    const requiredFields = [
        'first_name', 'last_name', 'email', 'phone',
        'address', 'city', 'state', 'zip', 'country'
    ];
    
    let isValid = true;
    const errors = [];
    
    requiredFields.forEach(field => {
        const input = document.getElementById(field) || document.querySelector(`[name="${field}"]`);
        if (input && !input.value.trim()) {
            isValid = false;
            errors.push(`${field.replace('_', ' ')} is required`);
            input.classList.add('is-invalid');
        } else if (input) {
            input.classList.remove('is-invalid');
        }
    });
    
    // Validate email format
    const emailInput = document.getElementById('email') || document.querySelector('[name="email"]');
    if (emailInput && emailInput.value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailInput.value)) {
            isValid = false;
            errors.push('Please enter a valid email address');
            emailInput.classList.add('is-invalid');
        }
    }
    
    // Check terms checkbox
    const termsCheckbox = document.getElementById('terms');
    if (termsCheckbox && !termsCheckbox.checked) {
        isValid = false;
        errors.push('You must agree to the terms and conditions');
    }
    
    if (!isValid) {
        showMessage(errors.join('<br>'), 'error');
    }
    
    return isValid;
}

/**
 * Show payment confirmation modal
 */
function showPaymentModal() {
    // Create modal HTML if it doesn't exist
    let modal = document.getElementById('paymentModal');
    
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'paymentModal';
        modal.className = 'modal fade';
        modal.setAttribute('tabindex', '-1');
        modal.setAttribute('aria-labelledby', 'paymentModalLabel');
        modal.setAttribute('aria-hidden', 'true');
        modal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="paymentModalLabel">Confirm Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <i class="fas fa-credit-card fa-3x text-primary mb-3"></i>
                            <h5>Simulated Payment</h5>
                            <p class="text-muted">This is a demo checkout. No actual payment will be processed.</p>
                        </div>
                        
                        <div class="card mb-3">
                            <div class="card-body">
                                <h6 class="card-title">Order Summary</h6>
                                <div id="paymentOrderSummary">
                                    <!-- Order summary will be inserted here -->
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Click "Yes, I've paid" to complete your order. This will simulate a successful payment.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmPaymentBtn">
                            <i class="fas fa-check me-2"></i>Yes, I've paid
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        
        // Add event listener for confirm button
        const confirmBtn = modal.querySelector('#confirmPaymentBtn');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                processCheckout();
            });
        }
    }
    
    // Update order summary in modal
    updatePaymentModalSummary(modal);
    
    // Show modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
}

/**
 * Update payment modal with order summary
 */
function updatePaymentModalSummary(modal) {
    const summaryDiv = modal.querySelector('#paymentOrderSummary');
    if (!summaryDiv) return;
    
    // Get totals from page
    const subtotalEl = document.querySelector('[data-subtotal]');
    const shippingEl = document.querySelector('[data-shipping]');
    const taxEl = document.querySelector('[data-tax]');
    const totalEl = document.querySelector('[data-total]');
    
    const subtotal = subtotalEl ? parseFloat(subtotalEl.dataset.subtotal || subtotalEl.textContent.replace(/[^\d.]/g, '')) : 0;
    const shipping = shippingEl ? parseFloat(shippingEl.dataset.shipping || shippingEl.textContent.replace(/[^\d.]/g, '')) : 0;
    const tax = taxEl ? parseFloat(taxEl.dataset.tax || taxEl.textContent.replace(/[^\d.]/g, '')) : 0;
    const total = totalEl ? parseFloat(totalEl.dataset.total || totalEl.textContent.replace(/[^\d.]/g, '')) : 0;
    
    summaryDiv.innerHTML = `
        <div class="d-flex justify-content-between mb-2">
            <span>Subtotal:</span>
            <span>₦${subtotal.toFixed(2)}</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span>Shipping:</span>
            <span>₦${shipping.toFixed(2)}</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span>Tax:</span>
            <span>₦${tax.toFixed(2)}</span>
        </div>
        <hr>
        <div class="d-flex justify-content-between fw-bold">
            <span>Total:</span>
            <span>₦${total.toFixed(2)}</span>
        </div>
    `;
}

/**
 * Process checkout after payment confirmation
 */
function processCheckout() {
    const confirmBtn = document.getElementById('confirmPaymentBtn');
    const checkoutForm = document.getElementById('checkoutForm');
    
    if (!checkoutForm) {
        showMessage('Checkout form not found', 'error');
        return;
    }
    
    // Disable button and show loading
    if (confirmBtn) {
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    }
    
    // Get form data
    const formData = new FormData(checkoutForm);
    formData.append('payment_method', document.querySelector('[name="payment_method"]:checked')?.value || 'simulated');
    
    // Process checkout
    fetch('actions/process_checkout_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close payment modal
            const paymentModal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
            if (paymentModal) {
                paymentModal.hide();
            }
            
            // Show success message and redirect
            showSuccessMessage(data);
            
            // Redirect to order confirmation after 2 seconds
            setTimeout(() => {
                window.location.href = `order_confirmation.php?order_id=${data.order_id}&invoice=${encodeURIComponent(data.invoice_no)}`;
            }, 2000);
        } else {
            // Re-enable button
            if (confirmBtn) {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<i class="fas fa-check me-2"></i>Yes, I\'ve paid';
            }
            
            showMessage(data.message || 'Checkout failed. Please try again.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Re-enable button
        if (confirmBtn) {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="fas fa-check me-2"></i>Yes, I\'ve paid';
        }
        
        showMessage('An error occurred during checkout. Please try again.', 'error');
    });
}

/**
 * Show success message
 */
function showSuccessMessage(data) {
    // Create success message
    const successDiv = document.createElement('div');
    successDiv.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
    successDiv.style.zIndex = '9999';
    successDiv.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle fa-2x me-3"></i>
            <div>
                <h5 class="mb-1">Order Placed Successfully!</h5>
                <p class="mb-0">Order Reference: <strong>${data.invoice_no || data.order_reference}</strong></p>
                <p class="mb-0 small">Redirecting to confirmation page...</p>
            </div>
        </div>
    `;
    
    document.body.appendChild(successDiv);
    
    // Remove after redirect
    setTimeout(() => {
        if (successDiv.parentNode) {
            successDiv.remove();
        }
    }, 3000);
}

/**
 * Show message to user
 */
function showMessage(message, type = 'info') {
    // Remove existing messages
    const existingMsg = document.querySelector('.checkout-message');
    if (existingMsg) {
        existingMsg.remove();
    }
    
    // Create message element
    const msgDiv = document.createElement('div');
    msgDiv.className = `alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} alert-dismissible fade show checkout-message`;
    msgDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    // Insert at top of checkout container
    const checkoutContainer = document.querySelector('.container.my-5, .container');
    if (checkoutContainer) {
        checkoutContainer.insertBefore(msgDiv, checkoutContainer.firstChild);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (msgDiv.parentNode) {
                msgDiv.remove();
            }
        }, 5000);
    } else {
        // Fallback: use alert
        alert(message);
    }
}

