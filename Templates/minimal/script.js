document.addEventListener('DOMContentLoaded', () => {

    const {__} = wp.i18n

    function calcWidgetInit(container) {

        const formElement = container.querySelector('form.convert-form');
        const categorySelect = container.querySelector('.category');
        const fromUnitSelect = container.querySelector('.fromUnit');
        const toUnitSelect = container.querySelector('.toUnit');
        const resultDiv = container.querySelector('.result');

        categorySelect.addEventListener('change', updateUnits);

        function updateUnits() {
            const category = categorySelect.value;
            fromUnitSelect.innerHTML = '';
            toUnitSelect.innerHTML = '';
            if (category && WP_Unit_Converter.categories && WP_Unit_Converter.categories[category]) {

                const units = WP_Unit_Converter.categories[category]?.units ?? {};

                for (let key in units) {
                    const unit = units[key];
                    const option = document.createElement('option');
                    option.value = key;
                    option.textContent = unit;
                    fromUnitSelect.appendChild(option.cloneNode(true));
                    toUnitSelect.appendChild(option);
                }
            }
        }

        formElement.addEventListener('submit', (event) => {

            event.preventDefault();

            const formData = new FormData(formElement);
            const data = {};
            for (const [key, value] of formData.entries()) {
                data[key] = value;
            }

            if (!data.category || !data.from || !data.to || !data.value) {
                resultDiv.textContent = __('Fill all fields.');
                return;
            }

            try {
                wp.apiFetch({path: 'unit-converter/v1/convert', method: 'POST', data}).then((response) => {
                    resultDiv.textContent = response.result;
                });
            } catch (error) {
                resultDiv.textContent = __('Error converting.');
            }
        });
    }


    document.querySelectorAll('.wpuc-widget').forEach((container) => calcWidgetInit(container));

});
