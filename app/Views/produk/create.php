<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('Assets/css/produk/create.css') ?>">

<div class="produk-container create-page">
    <h4>Daftar Produk &rsaquo; Tambah Produk</h4>
    <form action="<?= base_url('produk/store') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="form-group-row khusus-kategori-nama">
            <div class="form-group kategori">
                <label for="category_id">Kategori</label>
                <div class="input-wrapper">
                    <select name="category_id" id="category_id" class="form-control" required>
                        <option value="">Pilih kategori</option>
                        <?php foreach ($kategori as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= old('category_id') == $category['id'] ? 'selected' : '' ?>><?= $category['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if (isset($errors['category_id'])): ?>
                    <div class="text-danger"><?= $errors['category_id'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group nama-barang">
                <label for="name">Nama Barang</label>
                <div class="input-wrapper">
                    <input type="text" name="name" id="name" class="form-control" value="<?= old('name') ?>" placeholder="Masukan nama barang" required>
                </div>
                <?php if (isset($errors['name'])): ?>
                    <div class="text-danger"><?= $errors['name'] ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group-row">
            <div class="form-group">
                <label for="price_buy">Harga Beli</label>
                <input type="text" name="price_buy" id="price_buy" class="form-control" value="<?= old('price_buy') ?>" placeholder="Masukan harga beli" required>
                <?php if (isset($errors['price_buy'])): ?>
                    <div class="text-danger"><?= $errors['price_buy'] ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="price_sell">Harga Jual</label>
                <input type="text" name="price_sell" id="price_sell" class="form-control" value="<?= old('price_sell') ?>" placeholder="Masukan harga jual" readonly>
            </div>
            <div class="form-group">
                <label for="stock">Stok Barang</label>
                <input type="text" name="stock" id="stock" class="form-control" value="<?= old('stock') ?>" placeholder="Masukan jumlah stok barang" required>
                <?php if (isset($errors['stock'])): ?>
                    <div class="text-danger"><?= $errors['stock'] ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="image">Upload Image</label>
            <div class="image-upload" id="imageUpload">
            <input type="file" name="image" id="image" class="form-control" accept="image/png, image/jpeg" style="opacity: 0; position: absolute; width: 1px; height: 1px;">
                <div class="image-drop-area">
                    <i class="fas fa-image"></i>
                    <p>Upload gambar disini</p>
                    <img id="imagePreview" src="" />
                    <p id="fileName" class="file-name"></p>
                </div>
            </div>
        </div>

        <div class="form-footer">
            <button type="button" class="btn btn-cancel" onclick="window.location.href='<?= base_url('produk') ?>'">Batalkan</button>
            <button type="submit" class="btn btn-save">Simpan</button>
        </div>
    </form>
</div>

<script>
    const removeCommas = value => value.replace(/,/g, '');

    document.querySelector('form').addEventListener('submit', function(event) {
        let priceBuy = document.getElementById('price_buy').value;
        let priceSell = document.getElementById('price_sell').value;
        let stock = document.getElementById('stock').value;

        document.getElementById('price_buy').value = removeCommas(priceBuy);
        document.getElementById('price_sell').value = removeCommas(priceSell);
        document.getElementById('stock').value = removeCommas(stock);
    });

    const formatNumberWithCommas = value => {
        let num = value.replace(/[^0-9.]/g, ''); 
        return num.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    };

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

    const imageUpload = document.getElementById('imageUpload');
    const imageInput = document.getElementById('image');
    const imageDropArea = imageUpload.querySelector('.image-drop-area');
    const imagePreview = imageUpload.querySelector('#imagePreview');
    const fileNameElement = document.getElementById('fileName');

    const handleFile = (file) => {
        const reader = new FileReader();
        reader.onload = function (e) {
            imagePreview.style.display = 'block';
            imagePreview.src = e.target.result;
            imageDropArea.style.border = '2px solid #007bff';
            imageDropArea.querySelector('i').style.display = 'none';
            imageDropArea.querySelector('p').style.display = 'none';
            fileNameElement.textContent = file.name;
            fileNameElement.style.display = 'block';
        };
        reader.readAsDataURL(file);
    };

    imageInput.addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) handleFile(file);
    });

    imageDropArea.addEventListener('click', () => imageInput.click());

    imageUpload.addEventListener('dragover', (event) => event.preventDefault());
    imageUpload.addEventListener('dragleave', () => imageDropArea.classList.remove('dragover'));

    imageUpload.addEventListener('drop', (event) => {
        event.preventDefault();
        const file = event.dataTransfer.files[0];
        if (file) handleFile(file);
    });
</script>

<?= $this->endSection() ?>