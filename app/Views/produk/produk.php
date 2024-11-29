<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('Assets/css/produk/produk.css') ?>">

<div class="produk-container">
    <h4>Daftar Produk</h4>
    <div class="header">
        <div class="filter">
            <div class="search-wrapper">
                <input type="text" class="search-box" placeholder="Cari barang">
                <i class="fa fa-search search-icon"></i>
            </div>
            <div class="dropdown-wrapper">
                <select class="category-filter" id="categoryFilter">
                    <option value="semua">Semua</option>
                    <?php foreach ($kategori as $kat): ?>
                        <option value="<?= $kat['id'] ?>"><?= $kat['name'] ?></option>
                    <?php endforeach; ?>
                </select>
                <i class="fas fa-box dropdown-icon"></i>
            </div>
        </div>
        <div class="actions">
            <button class="btn-export" onclick="exportToExcel()">
                <i class="fa fa-file-excel"></i> Export Excel
            </button>
            <a href="<?= base_url('produk/create') ?>" class="btn-add">
                <i class="fa fa-plus"></i> Tambah Produk
            </a>
        </div>
    </div>

    <div class="table-responsive" id="produkTable">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Image</th>
                    <th>Nama Produk</th>
                    <th>Kategori Produk</th>
                    <th>Harga Beli (Rp)</th>
                    <th>Harga Jual (Rp)</th>
                    <th>Stok Produk</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($produk)): ?>
                    <?php $no = 1; ?>
                    <?php foreach ($produk as $product): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><img src="<?= base_url('Assets/img/produk/'.$product['image']) ?>" alt="Product Image" class="product-image"></td>
                            <td><?= $product['name'] ?></td>
                            <td><?= $product['category_name'] ?></td>
                            <td><?= number_format($product['price_buy'], 0, ',') ?></td>
                            <td><?= number_format($product['price_sell'], 0, ',') ?></td>
                            <td><?= $product['stock'] ?></td>
                            <td class="action-buttons">
                                <a href="<?= base_url('produk/edit/'.$product['id']) ?>" class="btn btn-edit" style="color: #007bff;">
                                    <i class="fa fa-pencil-alt"></i>
                                </a>
                                <a href="#" class="btn btn-delete" style="color: #f23a2e;" onclick="deleteProduct(<?= $product['id'] ?>)">
                                    <i class="fa fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data produk</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="table-footer">
        <div class="data-info">
            Show <?= count($produk) ?> from <?= $total_data ?> entries
        </div>
        <div class="pagination">
            <?= $pager->links('produk') ?>
        </div>
    </div>
</div>

<script>
    function fetchProducts(searchTerm = '', category = 'semua', page = 1) {
        fetch('<?= base_url('produk/filter') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            body: JSON.stringify({
                search: searchTerm,
                category: category,
                page: page
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const produkTable = document.querySelector('#produkTable tbody');
                const dataInfo = document.querySelector('.data-info');
                produkTable.innerHTML = data.tableHtml;

                dataInfo.innerHTML = `Show ${data.shownData} from ${data.totalFilteredData} entries`; 

                const pagination = document.querySelector('.pagination');
                pagination.innerHTML = '';
                for (let i = 1; i <= data.totalPages; i++) {
                    const pageItem = document.createElement('li');
                    pageItem.classList.add('page-item');
                    pageItem.innerHTML = `<a href="javascript:void(0)" onclick="fetchProducts('${searchTerm}', '${category}', ${i})">${i}</a>`;
                    pagination.appendChild(pageItem);
                }
            } else {
                console.error('Error:', data.message);
            }
        })
        .catch(error => console.log('Error fetching products:', error));
    }

    document.querySelector('.search-box').addEventListener('input', function () {
        fetchProducts(this.value, document.querySelector('.category-filter').value);
    });

    document.querySelector('.category-filter').addEventListener('change', function () {
        fetchProducts(document.querySelector('.search-box').value, this.value);
    });

    function exportToExcel() {
        const searchTerm = document.querySelector('.search-box').value;
        const category = document.querySelector('.category-filter').value;
        
        window.location.href = '<?= base_url('produk/exportExcel') ?>?search=' + searchTerm + '&category=' + category;
    }

    function deleteProduct(productId) {
        Swal.fire({
            title: 'Pemberitahuan!',
            text: 'Hapus Data Ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Hapus',
        }).then((result) => {
            if (result.isConfirmed) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url('produk/delete/') ?>' + productId;

                var csrfToken = '<?= csrf_token() ?>';
                var csrfHash = '<?= csrf_hash() ?>';
                var inputToken = document.createElement('input');
                inputToken.type = 'hidden';
                inputToken.name = csrfToken;
                inputToken.value = csrfHash;
                form.appendChild(inputToken);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }

</script>

<?= $this->endSection() ?>