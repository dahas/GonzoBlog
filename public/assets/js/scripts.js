
const ajax = {
    get: url => {
        const xhr = new XMLHttpRequest();
        xhr.responseType = "json";
        xhr.addEventListener("load", ev => {
            console.log(ev.currentTarget.response);
        });
        xhr.addEventListener("error", ev => {
            console.log("Request failed!");
        });
        xhr.open("GET", url);
        xhr.send();
    }
};

