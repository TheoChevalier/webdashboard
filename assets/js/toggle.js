function toggle(classname) {
    var locales = document.getElementsByClassName(classname);
    var current = locales[0].style.display;
    var state = (current === 'none')? '': 'none';

    for (var i = 0; i < locales.length; i ++) {
        locales[i].style.display = state;
    }
}
