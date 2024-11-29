<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('Assets/css/produk/edit.css') ?>">

<div class="produk-container edit-page">
    <h4>Daftar Produk &rsaquo; Edit Produk</h4>
    <form action="<?= base_url('produk/update/'.$product['id']) ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="form-group-row khusus-kategori-nama-edit">
            <div class="form-group kategori-edit">
                <label for="category_id">Kategori</label>
                <div class="input-wrapper">
                    <select name="category_id" id="category_id" class="form-control" required>
                        <option value="">Pilih kategori</option>
                        <?php foreach ($kategori as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= old('category_id', $product['category_id']) == $category['id'] ? 'selected' : '' ?>><?= $category['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if (isset($errors['category_id'])): ?>
                    <div class="text-danger"><?= $errors['category_id'] ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group nama-barang-edit">
                <label for="name">Nama Barang</label>
                <div class="input-wrapper">
                    <input type="text" name="name" id="name" class="form-control" value="<?= old('name', $product['name']) ?>" placeholder="Masukan nama barang" required>
                </div>
                <?php if (isset($errors['name'])): ?>
                    <div class="text-danger"><?= $errors['name'] ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group-row">
            <div class="form-group">
                <label for="price_buy">Harga Beli</label>
                <input type="text" name="price_buy" id="price_buy" class="form-control" value="<?= old('price_buy', $product['price_buy']) ?>" placeholder="Masukan harga beli" required>
                <?php if (isset($errors['price_buy'])): ?>
                    <div class="text-danger"><?= $errors['price_buy'] ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="price_sell">Harga Jual</label>
                <input type="text" name="price_sell" id="price_sell" class="form-control" value="<?= old('price_sell', $product['price_sell']) ?>" placeholder="Masukan harga jual"readonly>
            </div>
            <div class="form-group">
                <label for="stock">Stok Barang</label>
                <input type="text" name="stock" id="stock" class="form-control" value="<?= old('stock', $product['stock']) ?>" placeholder="Masukan jumlah stok barang" required>
                <?php if (isset($errors['stock'])): ?>
                    <div class="text-danger"><?= $errors['stock'] ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="image">Upload Image</label>
            <div class="image-upload-edit" id="imageUploadEdit">
                <input type="file" name="image" id="imageEdit" class="form-control" accept="image/png, image/jpeg" style="display: none;">
                <div class="image-drop-area-edit">
                    <img id="imagePreviewEdit" src="<?= base_url('Assets/img/produk/'.$product['image']) ?>" 
                        alt="Preview" style="display: <?= $product['image'] ? 'block' : 'none' ?>;">
                    <p id="fileNameEdit" class="file-name" style="display: <?= $product['image'] ? 'block' : 'none' ?>;">
                        <?= $product['image'] ?>
                    </p>
                    <p <?= $product['image'] ? 'style="display: none;"' : '' ?>>Upload gambar disini</p>
                </div>
            </div>
            <?php if (isset($errors['image'])): ?>
                <div class="text-danger"><?= $errors['image'] ?></div>
            <?php endif; ?>
        </div>

        <div class="form-footer">
            <button type="button" class="btn btn-cancel" onclick="window.location.href='<?= base_url('produk') ?>'">Batalkan</button>
            <button type="submit" class="btn btn-save">Simpan</button>
        </div>
    </form>
</div>

<script>
    const formatNumberWithCommas = value => {
        if (!value) return '';
        let num = value.toString().replace(/[^0-9.]/g, ''); 
        let [integer, decimal] = num.split('.'); 
        integer = integer.replace(/\B(?=(\d{3})+(?!\d))/g, ","); 
        if (decimal && parseInt(decimal) === 0) {
            return integer; 
        }
        return decimal ? `${integer}.${decimal}` : integer; 
    };

    const removeCommas = value => value.replace(/,/g, '');

    document.addEventListener('DOMContentLoaded', function () {
        const priceBuyInput = document.getElementById('price_buy');
        const priceSellInput = document.getElementById('price_sell');
        const stockInput = document.getElementById('stock');

        if (priceBuyInput) {
            priceBuyInput.value = formatNumberWithCommas(priceBuyInput.value);
        }
        if (priceSellInput) {
            priceSellInput.value = formatNumberWithCommas(priceSellInput.value);
        }
        if (stockInput) {
            stockInput.value = formatNumberWithCommas(stockInput.value);
        }
    });

    document.getElementById('price_buy').addEventListener('input', function () {
        let priceBuy = removeCommas(this.value);
        if (priceBuy) {
            priceBuy = parseFloat(priceBuy);
            if (!isNaN(priceBuy)) {
                let priceSell = priceBuy * 1.3;
                document.getElementById('price_sell').value = formatNumberWithCommas(priceSell.toFixed(0));
            }
        }
        this.value = formatNumberWithCommas(this.value);
    });

    document.getElementById('stock').addEventListener('input', function () {
        this.value = formatNumberWithCommas(removeCommas(this.value));
    });

    document.querySelector('form').addEventListener('submit', function () {
        const priceBuyInput = document.getElementById('price_buy');
        const priceSellInput = document.getElementById('price_sell');
        const stockInput = document.getElementById('stock');

        if (priceBuyInput) priceBuyInput.value = removeCommas(priceBuyInput.value);
        if (priceSellInput) priceSellInput.value = removeCommas(priceSellInput.value);
        if (stockInput) stockInput.value = removeCommas(stockInput.value);
    });

    const imageUploadEdit = document.getElementById('imageUploadEdit');
    const imageInputEdit = document.getElementById('imageEdit');
    const imageDropAreaEdit = imageUploadEdit.querySelector('.image-drop-area-edit');
    const imagePreviewEdit = imageUploadEdit.querySelector('#imagePreviewEdit');
    const fileNameElementEdit = document.getElementById('fileNameEdit');
    const placeholderText = imageDropAreaEdit.querySelector('p');

    const handleFileEdit = (file) => {
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            imagePreviewEdit.style.display = 'block';
            imagePreviewEdit.src = e.target.result;

            imageDropAreaEdit.style.border = '2px solid #007bff';
            if (placeholderText) placeholderText.style.display = 'none';

            fileNameElementEdit.textContent = file.name;
            fileNameElementEdit.style.display = 'block';
        };
        reader.readAsDataURL(file);
    };

    imageInputEdit.addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) handleFileEdit(file);
    });

    imageDropAreaEdit.addEventListener('click', () => imageInputEdit.click());

    imageUploadEdit.addEventListener('dragover', (event) => {
        event.preventDefault();
        imageDropAreaEdit.classList.add('dragover');
    });

    imageUploadEdit.addEventListener('dragleave', () => {
        imageDropAreaEdit.classList.remove('dragover');
    });

    imageUploadEdit.addEventListener('drop', (event) => {
        event.preventDefault();
        const file = event.dataTransfer.files[0];
        if (file) handleFileEdit(file);
    });

</script>

<?= $this->endSection() ?>