// Detect nearest branch for delivery based on selected address and update shipping cost
let currentShippingCost = 0;

function detectNearestBranchForDelivery(addressId) {
    const shippingBadge = document.getElementById('shippingBadge');
    
    // Show loading state
    if (shippingBadge) {
        shippingBadge.innerHTML = '<i class="spinner-border spinner-border-sm"></i> Menghitung...';
        shippingBadge.className = 'badge bg-secondary mt-2';
    }
    
    // Get address coordinates from the selected address
    fetch('/api/address/' + addressId, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.address && data.address.latitude && data.address.longitude) {
            // Find nearest branch
            fetch('/api/nearest-branch?latitude=' + data.address.latitude + '&longitude=' + data.address.longitude, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(branchData => {
                if (branchData.success && branchData.branch) {
                    const branch = branchData.branch;
                    currentShippingCost = branch.shipping_cost || 15000;
                    
                    console.log('Nearest branch for delivery:', branch.nama_cabang, 
                                '(' + branch.distance + ' km)', 
                                'Ongkir: Rp ' + formatNumber(currentShippingCost));
                    
                    // Update shipping badge
                    if (shippingBadge) {
                        shippingBadge.innerHTML = 'Biaya: Rp ' + formatNumber(currentShippingCost) + 
                                                  ' <small>(' + branch.distance + ' km)</small>';
                        shippingBadge.className = 'badge bg-info mt-2';
                    }
                    
                    // Update shipping cost in order summary if kurir is selected
                    const kurirRadio = document.getElementById('kurir');
                    if (kurirRadio && kurirRadio.checked) {
                        updateShippingCostDisplay(currentShippingCost);
                    }
                } else {
                    // Fallback to default shipping cost
                    currentShippingCost = 15000;
                    if (shippingBadge) {
                        shippingBadge.innerHTML = 'Biaya: Rp 15.000 (estimasi)';
                        shippingBadge.className = 'badge bg-warning mt-2';
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching nearest branch for delivery:', error);
                currentShippingCost = 15000;
                if (shippingBadge) {
                    shippingBadge.innerHTML = 'Biaya: Rp 15.000 (estimasi)';
                    shippingBadge.className = 'badge bg-warning mt-2';
                }
            });
        } else {
            // No coordinates available, use default
            currentShippingCost = 15000;
            if (shippingBadge) {
                shippingBadge.innerHTML = 'Biaya: Rp 15.000 (estimasi)';
                shippingBadge.className = 'badge bg-warning mt-2';
            }
        }
    })
    .catch(error => {
        console.error('Error fetching address:', error);
        currentShippingCost = 15000;
        if (shippingBadge) {
            shippingBadge.innerHTML = 'Biaya: Rp 15.000 (estimasi)';
            shippingBadge.className = 'badge bg-warning mt-2';
        }
    });
}

function updateShippingCostDisplay(cost) {
    const shippingCostValue = document.getElementById('shippingCostValue');
    const totalAmount = document.getElementById('totalAmount');
    const pointsEarned = document.getElementById('pointsEarned');
    
    // Update shipping cost display
    if (shippingCostValue) {
        shippingCostValue.textContent = 'Rp ' + formatNumber(cost);
    }
    
    // Recalculate total
    const subtotal = window.checkoutData.subtotal;
    const discount = window.checkoutData.discount;
    const membershipFee = window.checkoutData.membershipFee;
    
    const newTotal = subtotal - discount + membershipFee + cost;
    
    if (totalAmount) {
        totalAmount.textContent = 'Rp ' + formatNumber(newTotal);
    }
    
    // Update points if element exists
    if (pointsEarned) {
        const newPoints = Math.floor(newTotal / 20000);
        pointsEarned.textContent = newPoints;
    }
}
