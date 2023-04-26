
/**
 * Bootstrap Modal Popup
 * 
 * Used to confirm content deletion.
 */
const deleteModal = document.getElementById('confirmDelete');

deleteModal.addEventListener('show.bs.modal', function (event) {
    const elm = event.relatedTarget;

    document.getElementById('confirmDeleteLabel')
        .innerText = "Really delete this " + ucfirst(elm.dataset.ctype) + "?";

    const confirmButton = document.getElementById('confirmDeleteButton');
    confirmButton.addEventListener("click", () => {
        location.href = elm.href;
    });
});

// ----- Helper ----------------------

/**
 * Usage: ajax.get(<url>, <callbackSuccess>, <callbackError>);
 * 
 * Example:
 * 
 * ajax.get("/Blog/Test/123", response => {
 *    console.log(response);
 * });
 */
const ajax = {
    get: (url, success, error) => {
        const xhr = new XMLHttpRequest();
        xhr.responseType = "json";
        xhr.addEventListener("load", ev => {
            success(ev.currentTarget.response);
        });
        xhr.addEventListener("error", ev => {
            error(ev.currentTarget.response);
        });
        xhr.open("GET", url);
        xhr.send();
    }
};

/**
 * Makes the first letter of a string uppercase
 * 
 * @param {string} str 
 * @returns string
 */
function ucfirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}
