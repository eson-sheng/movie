let form = document.createElement('form');
let input = document.createElement('input');

input.name = 'filter';
input.id = 'search';
// input.placeholder = 'Type to search...';
input.placeholder = '请输入需要查找的名称...';

form.appendChild(input);

document.querySelector('h1').after(form);

let listItems = [].slice.call(document.querySelectorAll('.movie-list li'));

input.addEventListener('keyup', function () {
    let i,
        // e = "^(?=.*\\b" + this.value.trim().split(/\s+/).join("\\b)(?=.*\\b") + ").*$",
        e = this.value.trim().split(/\s+/).join("\\b)(?=.*\\b"),
        n = RegExp(e, "i");
    listItems.forEach(function(item) {
        item.removeAttribute('hidden');
    });
    listItems.filter(function(item) {
        i = item.querySelector('p').textContent.replace(/\s+/g, " ");
        return !n.test(i);
    }).forEach(function(item) {
        item.hidden = true;
    });
});

if (getQueryVariable('filter')) {
    input.value = getQueryVariable('filter');
    let i,
        // e = "^(?=.*\\b" + this.value.trim().split(/\s+/).join("\\b)(?=.*\\b") + ").*$",
        e = input.value.trim().split(/\s+/).join("\\b)(?=.*\\b"),
        n = RegExp(e, "i");
    listItems.forEach(function(item) {
        item.removeAttribute('hidden');
    });
    listItems.filter(function(item) {
        i = item.querySelector('p').textContent.replace(/\s+/g, " ");
        return !n.test(i);
    }).forEach(function(item) {
        item.hidden = true;
    });
}

function getQueryVariable(variable)
{
    let query = window.location.search.substring(1);
    let vars = query.split("&");
    for (let i=0;i<vars.length;i++) {
        let pair = vars[i].split("=");
        if(pair[0] === variable){return decodeURI(pair[1]);}
    }
    return(false);
}