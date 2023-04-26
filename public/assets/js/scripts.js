
// Usage: ajax.get(<url>);
const ajax = {
    get: url => {
        const xhr = new XMLHttpRequest();
        xhr.responseType = "json";
        // Success:
        xhr.addEventListener("load", ev => {
            console.log(ev.currentTarget.response);
        });
        // Error:
        xhr.addEventListener("error", ev => {
            console.log("Request failed!");
        });
        xhr.open("GET", url);
        xhr.send();
    }
};

// Modal Popup On Delete
const deleteModal = document.getElementById('confirmDelete');

deleteModal.addEventListener('show.bs.modal', function (event) {
    const elm = event.relatedTarget;

    document.getElementById('confirmDeleteLabel').innerText = "Really delete this " + ucfirst(elm.dataset.ctype) + "?";

    const confirmButton = document.getElementById('confirmDeleteButton');
    confirmButton.addEventListener("click", () => {
        location.href = elm.href;
    });
});

// ----- Helper ----------------------

/**
 * Make first letter uppercase
 * @param {string} str 
 * @returns string
 */
function ucfirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}
