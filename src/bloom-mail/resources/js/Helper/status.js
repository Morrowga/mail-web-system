// statusHelper.js
export function getStatusColor(status) {
    // Define a mapping of statuses to colors
    const statusColors = {
        'new': '#fc0214',
        'read': '#df517e',
        'confirming': '#fc0214',
        'confirmed': '#fc0214',
        'corresponding': '#fc0214',
        'replying': '#fc0214',
        'pending': '#186dc1',
    };

    // Return the color associated with the status or a default color if the status is unknown
    return statusColors[status] || '#000000';
}

// Helper function to get translated status names
export function getTranslatedStatus(t, statusName) {
    const mailStatus = [
        {
            name: 'new',
            value: t('table.new') // assuming `t()` is available in the component scope
        },
        {
            name: 'read',
            value: t('table.read') // assuming `t()` is available in the component scope
        },
        // Add more status values here as needed
    ];

    const status = mailStatus.find(status => status.name === statusName);
    // Return the translated value if found, else return the original statusName
    return status ? status.value : statusName;
}
