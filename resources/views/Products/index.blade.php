<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 30px;
        }
        .table-bordered {
            border: 1px solid #dee2e6;
        }
        th, td {
            padding: 12px;
            text-align: center;
            vertical-align: middle !important;
        }
        h1, h2 {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Product Form</h1>
        <form id="product-form" method="post" action="{{ route('products.store') }}">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity in Stock</label>
                <input type="number" class="form-control" id="quantity" name="quantity" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price per Item</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <h2 class="mt-5 mb-4">Submitted Products</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity in Stock</th>
                        <th>Price per Item</th>
                        {{-- <th>Datetime Submitted</th> --}}
                        <th>Total Value</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="product-table">
                    <!-- Products will be loaded here -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-product-form">
                        <input type="hidden" id="edit-product-id" name="id">
                        <div class="mb-3">
                            <label for="edit-name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="edit-name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-quantity" class="form-label">Quantity in Stock</label>
                            <input type="number" class="form-control" id="edit-quantity" name="quantity" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-price" class="form-label">Price per Item</label>
                            <input type="number" step="0.01" class="form-control" id="edit-price" name="price" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="save-edit">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            fetchProducts();
        });

        function fetchProducts() {
            $.ajax({
                url: '/fetch',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var tableBody = '';
                    var totalValueSum = 0;

                    if (data.length === 0) {
                        tableBody = '<tr><td colspan="6">No products found</td></tr>';
                    } else {
                        $.each(data, function(index, product) {
                            var totalValue = product.quantity * product.price;
                            tableBody += '<tr>';
                            tableBody += '<td>' + product.name + '</td>';
                            tableBody += '<td>' + product.quantity + '</td>';
                            tableBody += '<td>' + product.price + '</td>';
                            // tableBody += '<td>' + product.created_at + '</td>';
                            tableBody += '<td>' + totalValue + '</td>';
                            tableBody += '<td><button class="btn btn-sm btn-warning edit-product" data-id="' + product.id + '" data-bs-toggle="modal" data-bs-target="#editModal">Edit</button></td>';
                            tableBody += '</tr>';
                            totalValueSum += totalValue;
                        });

                        tableBody += '<tr>';
                        tableBody += '<td colspan="4">Total Value Sum</td>';
                        tableBody += '<td colspan="2">' + totalValueSum + '</td>';
                        tableBody += '</tr>';
                    }

                    $('#product-table').html(tableBody);
                }
            });
        }

        $(document).on('submit', '#product-form', function(event) {
            event.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                method: $(this).attr('method'),
                data: $(this).serialize(),
                success: function(response) {
                    fetchProducts();
                }
            });
        });

        $(document).on('click', '.edit-product', function() {
            var productId = $(this).data('id');
            $.ajax({
                url: '/products/' + productId,
                type: 'GET',
                dataType: 'json',
                success: function(product) {
                    $('#edit-product-id').val(product.id);
                    $('#edit-name').val(product.name);
                    $('#edit-quantity').val(product.quantity);
                    $('#edit-price').val(product.price);
                    $('#editModal').modal('show');
                }
            });
        });

        $(document).on('click', '#save-edit', function() {
            var productId = $('#edit-product-id').val();
            var formData = $('#edit-product-form').serialize();
            $.ajax({
                url: '/products/' + productId,
                type: 'PUT',
                data: formData,
                success: function(response) {
                    fetchProducts();
                    $('#editModal').modal('hide');
                }
            });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
