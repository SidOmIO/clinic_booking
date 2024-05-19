function getSelectedValues() {
    const selectedValues = [];
    const selects = document.querySelectorAll('.medication-select');
    selects.forEach(select => {
        if (select.value) {
            selectedValues.push(select.value);
        }
    });
    return selectedValues;
}

function generateOptions() {
    const options = [];
    initialOptions.forEach(option => {
        options.push(`<option value="${option.value}|${option.price}">${option.text}</option>`);
    });
    return options.join('');
}


function addDropdown() {
    const selectedValues = getSelectedValues();
    const dropdown = document.createElement("div");
    dropdown.classList.add("dropdown");
    dropdown.innerHTML = `
        <label for="medication">Select Medication:</label>
        <select name="medication[]" class="medication-select">
            <option disabled selected>Select your medicine:-</option>
            ${generateOptions(selectedValues)}
        </select>
        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity[]" min="1" value="1">
        <button type="button" onclick="removeDropdown(this)">Remove</button>
    `;
    
    document.getElementById("dropdownContainer").appendChild(dropdown);
}

function removeDropdown(button) {
    const dropdownContainer = document.getElementById("dropdownContainer");
    dropdownContainer.removeChild(button.parentNode);
}
