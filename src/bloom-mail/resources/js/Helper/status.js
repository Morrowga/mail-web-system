// statusHelper.js
export function getStatusColor(status) {
    // Define a mapping of statuses to colors
    const statusColors = {
        'new': '#fc0214',
        'read': '#df517e',
        'resolved': '#6fa7dc',
        'confirmed': '#0004fb',
        'replying': '#a600fe',
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
        {
            name: 'replying',
            value: t('table.replying')
        },
        {
            name: 'resolved',
            value: t('table.resolved')
        },
        {
            name: 'confirmed',
            value: t('table.confirmed')
        }
    ];

    const status = mailStatus.find(status => status.name === statusName);
    return status ? status.value : statusName;
}
