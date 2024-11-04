// status.js

// Function to get the color based on the status
export function getStatusColor(status) {
    // Define a mapping of statuses to colors
    const statusColors = {
        'new': '#fc0214',
        'currently_supporting': '#db69d0',
        'completion': '#186dc1',
    };

    // Return the color associated with the status or a default color if the status is unknown
    return statusColors[status] || '#000000';
}
