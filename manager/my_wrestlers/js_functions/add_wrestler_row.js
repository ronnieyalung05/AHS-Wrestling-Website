/* FOR ADDING WRESTLERS */

let rowTemplate = document.getElementById("template-row").cloneNode(true); // creating a variable that is a clone of our template row
                                                                            // we clone it so then it isn't directly linked to our template row

let myTableBody = document.getElementById("wrestler-table-body"); // creating a variable that links to our table

// Let's add some rows using a forloop
for (let i = 0; i < 4; i++) { // change i < n for more rows
    let clone = rowTemplate.cloneNode(true); // we clone AGAIN because we need to keep creating new rows

    myTableBody.appendChild(clone); // appending the clone to the end of the table
}

let addRowButton = document.getElementById("add-row");

addRowButton.addEventListener("click", function() {
    const tableBody = document.querySelector("#add-wrestler-table tbody");
    const newRow = document.createElement("tr");

    

    tableBody.appendChild(rowTemplate.cloneNode(true));
});

function removeRow_addForm(button) {
    const row = button.closest('tr');
    row.remove();
}

/* END */