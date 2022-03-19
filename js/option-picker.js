document.addEventListener('DOMContentLoaded', function () {
    const sectionClass = '.fireplace_optionPickerSection';
    const optionSections = document.querySelectorAll(sectionClass);
    for (let section of optionSections) {
        const optionVarName = section.getAttribute('data-options');
        const btn = section.querySelector('.fireplace_getOptionBtn');
        const showOptionEl = section.querySelector('.fireplace_showOption');
        if (btn && showOptionEl) {
            btn.addEventListener('click', () => {
                const option = getRandom(window[optionVarName]);
                showOptionEl.innerHTML = option.post_title;
            })
        }
    }
});

function getRandom(list) {
    return list[Math.floor(Math.random() * list.length)];
}