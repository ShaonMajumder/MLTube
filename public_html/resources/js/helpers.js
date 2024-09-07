
/**
 * Loading Authenticate Data
 */
const metaTag = document.querySelector('meta[name="auth-user-data"]');   
if (metaTag) {
    window.authUser = metaTag.getAttribute('data-auth-user') || '{}';
    metaTag.remove();
}
window.__auth = function () {
    try {
        return JSON.parse(window.authUser)
    } catch (error) {
        return null
    }
}

/**
 * Convert Rgb Color String to Hex
 * @param {*} rgbString 
 * @returns 
 */
window.rgbToHex = function (rgbString) {
    const rgbArray = rgbString.match(/\d+/g);
    const r = parseInt(rgbArray[0]);
    const g = parseInt(rgbArray[1]);
    const b = parseInt(rgbArray[2]);
    return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1).toUpperCase();
}
