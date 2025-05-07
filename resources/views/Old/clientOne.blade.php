<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Finishing Order Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .add-row {
            margin-top: 10px;
        }
        .remove-row {
            cursor: pointer;
            color: red;
        }
    </style>
</head>
<body>
    <h1>Product Finishing Order Form</h1>
    <form id="orderForm" action="/submit-order" method="POST">
        <table id="orderTable">
            <thead>
                <tr>
                    <th>Quantity</th>
                    <th>Item</th>
                    <th>Part Number</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="number" name="quantity[]" required></td>
                    <td><input type="text" name="item[]" required></td>
                    <td><input type="text" name="part_number[]" required></td>
                    <td><input type="text" name="description[]" required></td>
                    <td><input type="number" name="amount[]" required></td>
                    <td><span class="remove-row" onclick="removeRow(this)">Remove</span></td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="add-row" onclick="addRow()">Add Row</button>
        <button type="submit">Submit Order</button>
    </form>

    <script>
        function addRow() {
            const table = document.getElementById('orderTable').getElementsByTagName('tbody')[0];
            const newRow = table.insertRow();

            newRow.innerHTML = `
                <tr>
                    <td><input type="number" name="quantity[]" required></td>
                    <td><input type="text" name="item[]" required></td>
                    <td><input type="text" name="part_number[]" required></td>
                    <td><input type="text" name="description[]" required></td>
                    <td><input type="number" name="amount[]" required></td>
                    <td><span class="remove-row" onclick="removeRow(this)">Remove</span></td>
                </tr>
            `;
        }

        function removeRow(element) {
            const row = element.parentNode.parentNode;
            row.parentNode.removeChild(row);
        }
    </script>
</body>
</html>
