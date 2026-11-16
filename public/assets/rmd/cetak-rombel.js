(() => {
    const form = document.querySelector('#filter-rombel');
    const angkatan = form.elements.angkatan;
    const semester = form.elements.semester;
    const rombel = form.elements.rombel;

    const gantiPeriode = () => {
        rombel.value = '';
        if (angkatan.value && semester.value) {
            form.requestSubmit();
        }
    };

    angkatan.addEventListener('change', gantiPeriode);
    semester.addEventListener('change', gantiPeriode);
})();
