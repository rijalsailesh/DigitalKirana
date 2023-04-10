<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';

// check authentication
if (!checkAuth()) {
    header("Location: /?returnUrl=" . $_SERVER['REQUEST_URI']);
}

function getAllCustomers()
{
    //get all suppliers by tenant id
    $connection = ConnectionHelper::getConnection();
    $query = "select * from customer where TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getAllProducts()
{
    //get all products by tenant id
    $connection = ConnectionHelper::getConnection();
    $query = "select * from product where TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

$customers = getAllCustomers();
$products = getAllProducts();

$tenantId = getTenantId();
$loggedInUserId = getLoggedInUserId();
//adding to database

function generateUniqueBillNumber()
{
    $connection = ConnectionHelper::getConnection();
    $query = "select Number + 1 as Num from auto_generated_number where TenantId = :tenantId; update auto_generated_number set Number = Number + 1 where TenantId = :tenantId;";
    $statement = $connection->prepare($query);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetch();
    return $result['Num'];
}

$connection = ConnectionHelper::getConnection();
if (isPost()) {
    $connection->beginTransaction();

    //getting purchase
    $createdAt = $_POST['createdAt'];
    $customerId = $_POST['customerId'];
    $grossTotal = $_POST['grossTotal'];
    $discount = $_POST['discount'];
    $vat = $_POST['vat'];
    $netTotal = $_POST['netTotal'];
    $remarks = $_POST['remarks'];
    $tenderAmount = $_POST['tenderAmount'];
    $returnAmount = $_POST['returnAmount'];
    $customerName = $_POST['customerName'];



    //inerting into purchase table
    $connection = ConnectionHelper::getConnection();
    $query = "insert into sales (BillNumber, CustomerId, GrossTotal, Discount, Vat, NetTotal, Remarks, CreatedAt, UserId, TenantId, TenderAmount, ReturnAmount, CustomerName) values (:billNumber, :customerId, :grossTotal, :discount, :vat, :netTotal, :remarks, :createdAt, :userId, :tenantId, :tenderAmount, :returnAmount, :customerName)";
    $statement = $connection->prepare($query);
    $billNumber = generateUniqueBillNumber();
    $statement->bindParam('billNumber', $billNumber);
    $statement->bindParam('createdAt', $createdAt);
    $statement->bindParam('customerId', $customerId);
    $statement->bindParam('grossTotal', $grossTotal);
    $statement->bindParam('discount', $discount);
    $statement->bindParam('vat', $vat);
    $statement->bindParam('netTotal', $netTotal);
    $statement->bindParam('remarks', $remarks);
    $statement->bindParam('userId', $loggedInUserId);
    $statement->bindParam('tenantId', $tenantId);
    $statement->bindParam('tenderAmount', $tenderAmount);
    $statement->bindParam('returnAmount', $returnAmount);
    $statement->bindParam('customerName', $customerName);
    $statement->execute();

    $salesId = $connection->lastInsertId();

    function updateProductQuantity($productId, $quantity)
    {
        $connection = ConnectionHelper::getConnection();
        $query = "update product set Quantity = Quantity - :quantity where Id = :productId";
        $statement = $connection->prepare($query);
        $statement->bindParam('quantity', $quantity);
        $statement->bindParam('productId', $productId);
        $statement->execute();
    }

    if ($salesId > 0) {
        //inserting into purchase_details table
        $productIds = $_POST['productId'];
        $rates = $_POST['rate'];
        $quantities = $_POST['quantity'];
        $totalAmounts = $_POST['totalAmount'];

        for ($i = 0; $i < count($productIds); $i++) {
            $query = "insert into sales_details (SalesId, ProductId, Rate, Quantity, TotalAmount, TenantId) values (:salesId, :productId, :rate, :quantity, :totalAmount, :tenantId)";
            $connection = ConnectionHelper::getConnection();
            $statement = $connection->prepare($query);
            $statement->bindParam('salesId', $salesId);
            $statement->bindParam('productId', $productIds[$i]);
            $statement->bindParam('rate', $rates[$i]);
            $statement->bindParam('quantity', $quantities[$i]);
            $statement->bindParam('totalAmount', $totalAmounts[$i]);
            $statement->bindParam('tenantId', $tenantId);
            $statement->execute();
            $result = $statement->rowCount();
            if ($result > 0) {
                //updating product quantity
                updateProductQuantity($productIds[$i], $quantities[$i]);
            }
        }
        $connection->commit();
        addSuccessMessage("Sales done successfully");
        header("Location: /sales");
    }
}



require_once '../includes/themeHeader.php';
?>
<form action="" method="post" id="form">
    <div class="container-fluid">
        <a href="/sales" class="btn btn-primary"><i class="fas fa-fw fa-arrow-left"></i> View Sales</a>
        <div class="card mt-2 shadow-lg">
            <div class="card-header bg-primary">
                <h4 class="card-title text-light">Create Purchase</h4>
            </div>
            <div class="card-body">
                <?php renderMessages(); ?>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="date">Date</label>
                                <input type="date" id="date" name="createdAt" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label for="customer">Customer</label> <sup><button type="button" id="showCustomerBtn" class="border-0 d-inline p-0 bg-white text-info"><i class="fas fa-fw fa-info"></i></button></sup>
                                <select name="customerId" id="customer" class="form-control" required>
                                    <option value="">Select Customer</option>
                                    <?php
                                    foreach ($customers as $customer) :
                                    ?>
                                        <option value="<?= $customer['Id'] ?>">
                                            <?= $customer['CustomerName'] ?>
                                        </option>
                                    <?php
                                    endforeach;
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="customerName">Customer Name</label>
                                <input type="text" id="customerName" name="customerName" class="form-control" required>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="card mt-2">
                    <div class="card-header">
                        <h4 class="card-title">Product</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <label for="productCode">Code</label>
                                <input type="text" id="productCode" class="form-control" name="productCode">
                            </div>
                            <div class="col-md-2">
                                <label for="product">Product</label>
                                <select id="product" class="form-control">
                                    <option value="">Select Product</option>
                                    <?php
                                    foreach ($products as $product) :
                                    ?>
                                        <option value="<?= $product['Id'] ?>">
                                            <?= $product['ProductName'] ?>
                                        </option>
                                    <?php
                                    endforeach;
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="rate">Rate</label>
                                <input type="number" id="rate" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label for="quantity">Quantity</label>
                                <input type="number" id="quantity" class="form-control">
                                <span class="text-danger" id="stock"></span>
                            </div>
                            <div class="col-md-2">
                                <label for="amount">Amount</label>
                                <input type="number" id="amount" readonly class="form-control">
                            </div>
                            <div class="col-md-2 align-item-end">
                                <label for="addProductBtn">&nbsp;</label>
                                <button type="button" id="addProductBtn" class="btn btn-primary btn-block"><i class="fas fa-fw fa-plus"></i> Add</button>
                            </div>
                        </div>

                        <div class="row d-none" id="productContainer">
                            <table class="col-12 table table-bordered table-striped table-hover my-4">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Code</th>
                                        <th>Product Name</th>
                                        <th>Rate</th>
                                        <th>Quantity</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody"></tbody>
                            </table>

                            <div class="calculation col-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-4 mb-3">
                                                <label for="grossAmount">Gross Total</label>
                                            </div>
                                            <div class="col-8 mb-3">
                                                <input type="number" min="0" readonly name="grossTotal" id="grossTotal" class="form-control">
                                            </div>
                                            <div class="col-4 mb-3">
                                                <label for="discount">Discount(%)</label>
                                            </div>
                                            <div class="col-8 mb-3">
                                                <input type="number" min="0" id="discount" value="0" name="discount" class="form-control">
                                            </div>
                                            <div class="col-4 mb-3">
                                                <label for="vat">Vat(%)</label>
                                            </div>
                                            <div class="col-8 mb-3">
                                                <input type="number" min="0" id="vat" value="0" name="vat" class="form-control">
                                            </div>


                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-4 mb-3">
                                                <label for="netTotal">Net Total</label>
                                            </div>
                                            <div class="col-8 mb-3">
                                                <input type="number" min="0" readonly id="netTotal" name="netTotal" class="form-control">
                                            </div>
                                            <div class="col-4 mb-3">
                                                <label for="tenderAmount">Tender Amount</label>
                                            </div>
                                            <div class="col-8 mb-3">
                                                <input type="number" min="0" value="0" id="tenderAmount" class="form-control" name="tenderAmount">
                                            </div>
                                            <div class="col-4 mb-3">
                                                <label for="returnAmount">Return Amount</label>
                                            </div>
                                            <div class="col-8">
                                                <input type="number" min="0" readonly id="returnAmount" class="form-control" name="returnAmount">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="remarks">Remarks</label>
                                <textarea name="remarks" id="remarks" cols="30" rows="3" class="form-control" placeholder="Enter Remarks.."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-none" id="cardFooter">
                <button type="submit" id="submitBtn" class="btn btn-primary btn-block">Make Sales</button>
            </div>
        </div>
    </div>
</form>

<!-- supplier Modal-->
<div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Customer</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <label for="supplierName">Customer Name</label>
                    <input disabled type="text" name="supplierName" id="modal_customerName" class="form-control" placeholder="Customer Name" value="<?= $customer['CustomerName'] ?>" required>
                </div>
                <div class="mb-4">
                    <label for="phone">Phone</label>
                    <input disabled type="phone" name="phone" id="modal_phone" class="form-control" placeholder="Phone" value="<?= $customer['Phone'] ?>" required>
                </div>
                <div class="mb-4">
                    <label for="email">Email</label>
                    <input disabled type="email" name="email" id="modal_email" class="form-control" placeholder="Email" value="<?= $customer['Email'] ?>" required>
                </div>
                <div class="mb-4">
                    <label for="address">Address</label>
                    <textarea disabled name="address" id="modal_address" class="form-control" placeholder="Address" rows="4"><?= $customer['Address'] ?></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="button" data-dismiss="modal">Ok</button>
            </div>
        </div>
    </div>
</div>

<!-- table template for product -->
<template class="table-row-template">
    <tr>
        <input type="hidden" name="productId[]" class="form-control productId" />
        <td>
            <input type="text" readonly class="form-control productCode" />
        </td>
        <td>
            <input type="text" readonly class="form-control productName" />
        </td>
        <td>
            <input type="number" readonly name="rate[]" class="form-control rate" />
        </td>
        <td>
            <input type="number" readonly name="quantity[]" class="form-control quantity" />
        </td>
        <td>
            <input type="number" readonly name="totalAmount[]" class="form-control amount" />
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm deleteBtn"><i class="fas fa-fw fa-trash"></i></button>
        </td>
    </tr>
</template>

<script>
    //prevent submit form on enter key press
    document.addEventListener("DOMContentLoaded", function() {
        var form = document.getElementById("form");
        form.addEventListener('keypress', function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
            }
        }, true);

        //adding today's date
        const date = document.querySelector("#date");
        date.value = new Date().toISOString().substr(0, 10);
    });

    //get customer details when customer is selected and show in customer Name
    const customer = document.querySelector("#customer");
    customer.addEventListener("change", () => {
        //get data for supplier from database and show in modal
        const customerDetails = fetch(`http://digitalkirana/api/getCustomer.php?id=${customer.value}`)
            .then(response => response.json())
            .then(data => {
                document.querySelector("#customerName").value = data.CustomerName;
            })
    });


    // shwing popup for supplier details
    const showCustomerBtn = document.querySelector("#showCustomerBtn");
    showCustomerBtn.addEventListener("click", () => {
        //check if supplier is selected
        if (customer.value == "" || customer.value == null) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please select customer first!',
            })
            return;
        }

        //get data for supplier from database and show in modal
        function getCustomerById(id) {
            const customerDetails = fetch(`http://digitalkirana/api/getCustomer.php?id=${customer.value}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modal_customerName').value = data.CustomerName;
                    document.getElementById('modal_phone').value = data.Phone;
                    document.getElementById('modal_email').value = data.Email;
                    document.getElementById('modal_address').value = data.Address;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
        getCustomerById(customer.value);
        $('#customerModal').modal('show');
    });

    //function to clear products fields
    function clearProductFields() {
        document.querySelector('#productCode').value = "";
        document.querySelector('#product').value = "";
        document.querySelector('#rate').value = "";
        document.querySelector('#stock').innerHTML = "";
        document.querySelector('#quantity').value = "";
        document.querySelector('#amount').value = "";
    }

    //get product from database when productCode is entered
    const productCode = document.querySelector("#productCode");
    productCode.addEventListener("change", () => {
        const product = document.querySelector("#product");
        const rate = document.querySelector("#rate");
        const stock = document.querySelector("#stock");
        const quantity = document.querySelector("#quantity");
        const amount = document.querySelector("#amount");

        //check if productCode is entered
        if (productCode.value == "" || productCode.value == null) {
            clearProductFields();
            return;
        }

        const productDetails = fetch(`http://digitalkirana/api/getProduct.php?code=${productCode.value}`)
            .then(response => response.json())
            .then(data => {
                product.value = data.Id;
                rate.value = data.CostPrice;
                stock.innerHTML = data.Quantity;
                quantity.value = 0;
                amount.value = 0;
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });


    // get product from prdocut select box  
    const product = document.querySelector("#product");
    product.addEventListener("change", () => {

        const productCode = document.querySelector("#productCode");
        const rate = document.querySelector("#rate");
        const stock = document.querySelector("#stock");
        const quantity = document.querySelector("#quantity");
        const amount = document.querySelector("#amount");

        //check if product is selected
        if (product.value == "" || product.value == null) {
            clearProductFields();
            return;
        }

        const productDetails = fetch(`http://digitalkirana/api/getProduct.php?id=${product.value}`)
            .then(response => response.json())
            .then(data => {
                productCode.value = data.ProductCode;
                rate.value = data.SellingPrice;
                stock.innerHTML = data.Quantity;
                quantity.value = 0;
                amount.value = 0;
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });

    //calculate amount on rate change


    const rate = document.querySelector("#rate");
    rate.addEventListener("change", () => {
        const quantity = document.querySelector("#quantity");
        const amount = document.querySelector("#amount");
        amount.value = rate.value * quantity.value;
    });

    //calculate amount on quantity change
    const quantity = document.querySelector("#quantity");
    quantity.addEventListener("change", () => {
        const rate = document.querySelector("#rate");
        const amount = document.querySelector("#amount");
        amount.value = parseFloat(rate.value).toFixed(2) * quantity.value;
    });


    // function to check stock before adding to rows
    function checkStock() {
        const stock = document.querySelector("#stock");
        //get all quantity from table by productId and check if stock is available
        const tableBody = document.querySelector('#tableBody');
        const tableRows = tableBody.querySelectorAll('tr');
        let flag = true;

        //get all quantity from table by productId and add to totalQuantity
        let totalQuantity = 0;
        tableRows.forEach(row => {
            if (row.querySelector('.productId').value == product.value) {
                totalQuantity += parseInt(row.querySelector('.quantity').value);
            }
        });

        //check if stock is available
        if (parseInt(stock.innerHTML) < (parseInt(quantity.value) + totalQuantity)) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Stock is not available!',
            })
            flag = false;
        }

        return flag;
    }


    //add product to table
    const addProductBtn = document.querySelector("#addProductBtn");
    const amount = document.querySelector("#amount");

    const templateRef = document.querySelector('.table-row-template');
    const tableBody = document.querySelector('#tableBody');


    addProductBtn.addEventListener('click', (e) => {

        //check if product is selected
        if (product.value == "" || product.value == null) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please select product first!',
            })
            return;
        }

        //check if quantity is selected
        if (quantity.value == "" || quantity.value == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please enter quantity greater than 0',
            })
            return;
        }

        //check if stock is available
        if (!checkStock()) {
            return;
        }

        //make productContainer visible

        document.querySelector('#productContainer').classList.add('d-block');
        document.querySelector('#cardFooter').classList.add('d-block');

        //increase quantity if product and rate is same else add new row
        const rows = tableBody.querySelectorAll('tr');
        let isProductExist = false;
        rows.forEach(row => {
            if (row.querySelector('.productId').value == product.value && row.querySelector('.rate').value == rate.value) {
                isProductExist = true;
                row.querySelector('.quantity').value = parseInt(row.querySelector('.quantity').value) + parseInt(quantity.value);
                row.querySelector('.amount').value = parseFloat(row.querySelector('.amount').value) + parseFloat(amount.value);
            }
        })

        if (isProductExist) {
            clearProductFields();
            calculateGrossTotal()
            return;
        }

        //add new row to table
        const cloneNode = templateRef.content.cloneNode(true);
        cloneNode.querySelector('.productId').value = product.value;
        cloneNode.querySelector('.productCode').value = productCode.value;
        cloneNode.querySelector('.productName').value = product.options[product.selectedIndex].text;;
        cloneNode.querySelector('.rate').value = rate.value;
        cloneNode.querySelector('.quantity').value = quantity.value;
        cloneNode.querySelector('.amount').value = amount.value;
        tableBody.appendChild(cloneNode);

        //clear product select box
        clearProductFields();
        calculateGrossTotal();
    })


    //delete product row from table when delete button is clicked
    tableBody.addEventListener('click', (e) => {
        if (e.target.classList.contains('deleteBtn')) {
            e.target.closest('tr').remove();
            //check if table is empty
            if (tableBody.querySelectorAll('tr').length == 0) {
                document.querySelector('#tenderAmount').value = 0;
                document.querySelector('#returnAmount').value = 0;

            }
            calculateGrossTotal();
        }
    })

    //calculate net total after discount and vat
    function calculateNetTotal() {
        const netTotal = document.querySelector("#netTotal");
        const grossTotal = document.querySelector("#grossTotal");
        const grossTotalAfterDiscount = grossTotal.value - (discount.value * grossTotal.value / 100);
        netTotal.value = (grossTotalAfterDiscount + (vat.value * grossTotalAfterDiscount / 100)).toFixed(2);
    }

    //calculate gross total
    function calculateGrossTotal() {
        const rows = tableBody.querySelectorAll('tr');
        let grossTotal = 0;
        rows.forEach(row => {
            grossTotal += parseFloat(row.querySelector('.amount').value);
        })
        document.querySelector('#grossTotal').value = grossTotal.toFixed(2);
        calculateNetTotal();
    }


    //calculate net Total on discount change
    const discount = document.querySelector("#discount");
    discount.addEventListener("change", () => {
        calculateNetTotal();
    });

    //calculate net total on vat change after deducting discount
    const vat = document.querySelector("#vat");
    vat.addEventListener("change", () => {
        calculateNetTotal();
    });


    //calculate return amount on tender amount change
    const tenderAmount = document.querySelector("#tenderAmount");
    tenderAmount.addEventListener("change", () => {
        const netTotal = document.querySelector("#netTotal");
        const returnAmount = document.querySelector("#returnAmount");
        returnAmount.value = (tenderAmount.value - netTotal.value).toFixed(2);
    });


    //validation for submit
    const validation = () => {
        if (tableBody.querySelectorAll('tr').length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please add product first!',
            })
            return false;
        } else if (tenderAmount.value <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please add tender amount',
            })
            return false;
        }
        return true;
    }

    const submitBtn = document.querySelector("#submitBtn");
    submitBtn.addEventListener('click', (e) => {
        if (!validation()) {
            e.preventDefault();
        }
    })
</script>
<?php
require_once '../includes/themeFooter.php';
?>