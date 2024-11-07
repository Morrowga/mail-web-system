// status.js

// Function to get the color based on the status
export function getStatusColor(status) {
    let fixedStatus = status.toLowerCase();
    // Define a mapping of statuses to colors
    const statusColors = {
        'new': '#fc0214',
        'read': '#fc0214',
        'confirming': '#fc0214',
        'confirmed': '#fc0214',
        'corresponding': '#fc0214',
        'replying': '#fc0214',
        'pending': '#186dc1',
    };

    // Return the color associated with the status or a default color if the status is unknown
    return statusColors[status] || '#000000';
}
