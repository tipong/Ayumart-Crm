/**
 * Ticket Notification System - Real-time Updates via Polling
 *
 * This module handles:
 * - Polling for unread ticket count updates
 * - Badge updates without page reload
 * - Ticket status color updates
 * - Desktop notifications (optional)
 */

class TicketNotificationManager {
    constructor(options = {}) {
        this.pollInterval = options.pollInterval || 10000; // 10 seconds
        this.lastCheckTimestamp = Math.floor(Date.now() / 1000);
        this.pollingActive = false;
        this.badgeSelector = options.badgeSelector || '[data-badge-tickets]';
        this.ticketRowSelector = options.ticketRowSelector || '[data-ticket-id]';
        this.enableDesktopNotifications = options.enableDesktopNotifications || false;
        this.apiUrl = options.apiUrl || '/api';

        this.init();
    }

    /**
     * Initialize the notification manager
     */
    init() {
        // Request permission for desktop notifications if enabled
        if (this.enableDesktopNotifications && 'Notification' in window) {
            if (Notification.permission === 'default') {
                Notification.requestPermission();
            }
        }

        // Start polling
        this.startPolling();

        // Listen for page visibility changes to pause/resume polling
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.stopPolling();
            } else {
                this.startPolling();
            }
        });
    }

    /**
     * Start polling for new notifications
     */
    startPolling() {
        if (this.pollingActive) return;

        this.pollingActive = true;
        this.poll();
    }

    /**
     * Stop polling for new notifications
     */
    stopPolling() {
        if (this.pollTimer) {
            clearTimeout(this.pollTimer);
        }
        this.pollingActive = false;
    }

    /**
     * Poll for new notifications
     */
    poll() {
        if (!this.pollingActive) return;

        this.checkForNewNotifications()
            .then(() => {
                // Schedule next poll
                if (this.pollingActive) {
                    this.pollTimer = setTimeout(() => this.poll(), this.pollInterval);
                }
            })
            .catch(error => {
                console.error('Polling error:', error);
                // Continue polling even on error
                if (this.pollingActive) {
                    this.pollTimer = setTimeout(() => this.poll(), this.pollInterval);
                }
            });
    }

    /**
     * Check for new notifications
     */
    async checkForNewNotifications() {
        try {
            const response = await fetch(
                `${this.apiUrl}/notifications/new?since=${this.lastCheckTimestamp}`,
                {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                }
            );

            if (!response.ok) {
                throw new Error(`API error: ${response.status}`);
            }

            const data = await response.json();
            this.lastCheckTimestamp = Math.floor(Date.now() / 1000);

            console.log('Polling result:', {
                new_notifications: data.new_notifications,
                unread_count: data.unread_count,
                tickets: data.tickets
            });

            // ALWAYS update badge and ticket rows regardless of new_notifications flag
            // This ensures UI stays in sync with backend state
            await this.updateBadge(data.unread_count);
            await this.updateTicketRows();

            // Show notification if there are new updates
            if (data.new_notifications) {
                this.showDesktopNotification(data.unread_count);
            }

            return data;
        } catch (error) {
            console.error('Error checking for notifications:', error);
            throw error;
        }
    }

    /**
     * Get unread count and update badge
     */
    async updateBadge(count = null) {
        try {
            // If count not provided, fetch it
            if (count === null) {
                const response = await fetch(`${this.apiUrl}/notifications/unread-count`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) throw new Error(`API error: ${response.status}`);

                const data = await response.json();
                count = data.unread_count || 0;
            }

            // Update badge elements
            const badges = document.querySelectorAll(this.badgeSelector);
            badges.forEach(badge => {
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            });

            return count;
        } catch (error) {
            console.error('Error updating badge:', error);
        }
    }

    /**
     * Get unread tickets and update visual status
     */
    async updateTicketRows() {
        try {
            const response = await fetch(`${this.apiUrl}/notifications/unread-tickets`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) throw new Error(`API error: ${response.status}`);

            const data = await response.json();
            const unreadTicketIds = data.tickets.map(t => t.id);

            // Update all ticket rows
            const ticketRows = document.querySelectorAll(this.ticketRowSelector);
            ticketRows.forEach(row => {
                const ticketId = parseInt(row.getAttribute('data-ticket-id'));

                if (unreadTicketIds.includes(ticketId)) {
                    // Mark as unread - check which page (CS or Customer)
                    row.classList.add('table-unread');

                    // Detect if CS page (blue) or Customer page (light blue)
                    // CS uses: #e7f3ff + #0066cc
                    // Customer uses: #e3f2fd + #1976d2
                    const pageType = document.body.getAttribute('data-page-type') || 'customer';

                    if (pageType === 'cs' || document.querySelector('[href*="/cs/tickets"]')) {
                        // CS page colors
                        row.style.backgroundColor = '#e7f3ff';
                        row.style.borderLeft = '4px solid #0066cc';
                    } else {
                        // Customer page colors
                        row.style.backgroundColor = '#e3f2fd';
                        row.style.borderLeft = '4px solid #1976d2';
                    }
                } else {
                    // Mark as read
                    row.classList.remove('table-unread');
                    row.style.backgroundColor = '';
                    row.style.borderLeft = '';
                }
            });
        } catch (error) {
            console.error('Error updating ticket rows:', error);
        }
    }

    /**
     * Mark ticket as read
     */
    async markTicketAsRead(ticketId) {
        try {
            const response = await fetch(`${this.apiUrl}/notifications/mark-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                },
                credentials: 'same-origin',
                body: JSON.stringify({ ticket_id: ticketId })
            });

            if (!response.ok) throw new Error(`API error: ${response.status}`);

            const data = await response.json();

            if (data.success) {
                // Update visual status
                const row = document.querySelector(`[data-ticket-id="${ticketId}"]`);
                if (row) {
                    row.classList.remove('table-unread');
                    row.style.backgroundColor = '';
                    row.style.borderLeft = '';
                }

                // Update badge
                await this.updateBadge();
            }

            return data;
        } catch (error) {
            console.error('Error marking ticket as read:', error);
        }
    }

    /**
     * Show desktop notification
     */
    showDesktopNotification(unreadCount) {
        if (!this.enableDesktopNotifications || !('Notification' in window)) {
            return;
        }

        if (Notification.permission !== 'granted') {
            return;
        }

        try {
            new Notification('Notifikasi Tiket', {
                icon: '/images/notification-icon.png',
                body: `Anda memiliki ${unreadCount} tiket yang belum dibaca`,
                tag: 'ticket-notification',
                requireInteraction: false
            });
        } catch (error) {
            console.error('Error showing desktop notification:', error);
        }
    }

    /**
     * Get CSRF token from meta tag or cookie
     */
    getCsrfToken() {
        // Try meta tag first
        let token = document.querySelector('meta[name="csrf-token"]');
        if (token) return token.getAttribute('content');

        // Try from cookie
        const cookies = document.cookie.split(';');
        for (let cookie of cookies) {
            const [name, value] = cookie.trim().split('=');
            if (name === 'XSRF-TOKEN') {
                return decodeURIComponent(value);
            }
        }

        return '';
    }

    /**
     * Destroy the notification manager
     */
    destroy() {
        this.stopPolling();
    }
}

// Auto-initialize on page load if data attributes exist
document.addEventListener('DOMContentLoaded', function() {
    const body = document.body;

    // Check if notification manager should be auto-initialized
    if (body.getAttribute('data-enable-notifications') === 'true') {
        const notificationManager = new TicketNotificationManager({
            pollInterval: parseInt(body.getAttribute('data-poll-interval')) || 10000,
            badgeSelector: body.getAttribute('data-badge-selector') || '[data-badge-tickets]',
            ticketRowSelector: body.getAttribute('data-ticket-row-selector') || '[data-ticket-id]',
            enableDesktopNotifications: body.getAttribute('data-desktop-notifications') === 'true',
            apiUrl: body.getAttribute('data-api-url') || '/api'
        });

        // Store globally for manual access if needed
        window.ticketNotificationManager = notificationManager;
    }
});
