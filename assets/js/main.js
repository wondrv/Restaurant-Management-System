// Restaurant Management System - Main JavaScript

$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Confirm delete actions
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const itemType = $(this).data('type') || 'item';
        
        if (confirm(`Are you sure you want to delete this ${itemType}? This action cannot be undone.`)) {
            window.location.href = url;
        }
    });

    // Search functionality
    $('#searchInput').on('keyup', function() {
        const searchTerm = $(this).val();
        if (searchTerm.length >= 3 || searchTerm.length === 0) {
            searchItems(searchTerm);
        }
    });

    // Form validation
    $('form').on('submit', function(e) {
        const requiredFields = $(this).find('[required]');
        let isValid = true;

        requiredFields.each(function() {
            if ($(this).val().trim() === '') {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            showAlert('Please fill in all required fields.', 'danger');
        }
    });

    // Image preview
    $('.image-upload').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('.image-preview').attr('src', e.target.result).show();
            };
            reader.readAsDataURL(file);
        }
    });

    // Dynamic price calculation
    $('.quantity-input').on('change', function() {
        calculateTotal();
    });

    // Toggle availability
    $('.availability-toggle').on('change', function() {
        const itemId = $(this).data('id');
        const isAvailable = $(this).is(':checked');
        updateAvailability(itemId, isAvailable);
    });
});

// Search function with AJAX
function searchItems(searchTerm) {
    $.ajax({
        url: window.location.pathname,
        type: 'GET',
        data: { search: searchTerm },
        success: function(response) {
            $('#searchResults').html(response);
        },
        error: function() {
            showAlert('Search failed. Please try again.', 'danger');
        }
    });
}

// Show alert messages
function showAlert(message, type = 'info') {
    const alertHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('#alertContainer').html(alertHTML);
    
    // Auto-hide after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
}

// Calculate order total
function calculateTotal() {
    let total = 0;
    $('.order-item').each(function() {
        const price = parseFloat($(this).find('.item-price').data('price'));
        const quantity = parseInt($(this).find('.quantity-input').val());
        const itemTotal = price * quantity;
        $(this).find('.item-total').text('$' + itemTotal.toFixed(2));
        total += itemTotal;
    });
    $('#orderTotal').text('$' + total.toFixed(2));
}

// Update item availability
function updateAvailability(itemId, isAvailable) {
    $.ajax({
        url: '../includes/update_availability.php',
        type: 'POST',
        data: {
            id: itemId,
            availability: isAvailable ? 1 : 0,
            csrf_token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            const result = JSON.parse(response);
            if (result.success) {
                showAlert('Availability updated successfully.', 'success');
            } else {
                showAlert('Failed to update availability.', 'danger');
            }
        },
        error: function() {
            showAlert('Update failed. Please try again.', 'danger');
        }
    });
}

// File upload with preview
function handleFileUpload(input, previewContainer) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            $(previewContainer).html(`
                <img src="${e.target.result}" class="img-thumbnail" style="max-width: 200px;">
            `);
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Pagination
function goToPage(page) {
    const url = new URL(window.location);
    url.searchParams.set('page', page);
    window.location.href = url.toString();
}

// Export data
function exportData(format = 'csv') {
    const url = new URL(window.location);
    url.searchParams.set('export', format);
    window.open(url.toString(), '_blank');
}

// Print functionality
function printPage() {
    window.print();
}

// Sort table columns
function sortTable(column, direction = 'asc') {
    const url = new URL(window.location);
    url.searchParams.set('sort', column);
    url.searchParams.set('order', direction);
    window.location.href = url.toString();
}

// Filter by category
function filterByCategory(categoryId) {
    const url = new URL(window.location);
    if (categoryId) {
        url.searchParams.set('category', categoryId);
    } else {
        url.searchParams.delete('category');
    }
    window.location.href = url.toString();
}

// Auto-save form data
function autoSaveForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return;

    const formData = new FormData(form);
    const data = {};
    
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    
    localStorage.setItem(`form_${formId}`, JSON.stringify(data));
}

// Restore form data
function restoreFormData(formId) {
    const savedData = localStorage.getItem(`form_${formId}`);
    if (!savedData) return;

    const data = JSON.parse(savedData);
    const form = document.getElementById(formId);
    
    Object.keys(data).forEach(key => {
        const field = form.querySelector(`[name="${key}"]`);
        if (field) {
            field.value = data[key];
        }
    });
}

// Clear saved form data
function clearSavedFormData(formId) {
    localStorage.removeItem(`form_${formId}`);
}