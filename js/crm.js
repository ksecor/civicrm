function showHide(sections, showSections) {
    // first hide all the sections
    for (var i = 0; i < sections.length; i++) {
       document.getElementById(sections[i]).style.display = 'none';
    }

    // now show all the sections that should be visible
    for (var i = 0; i < showSections.length; i++) {
       document.getElementById(showSections[i]).style.display = 'none';
    }
}

function show(sections, section) {
    for (var i = 0; i < sections.length; i++) {
        if (sections[i] == section) {
            document.getElementById(sections[i]).style.display = 'block';
        }
    }
}

function hide(sections, section) {
    for (var i = 0; i < sections.length; i++) {
        if (sections[i] == section) {
            document.getElementById(sections[i]).style.display = 'none';
        }
    }
}
