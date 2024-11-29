<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('Assets/css/profil/profil.css') ?>">

<div class="profil-container">
    <form action="<?= base_url('profil/update') ?>" method="POST" enctype="multipart/form-data">
        <div class="profil-header">
            <div class="profile-pic-wrapper">
                <img id="profile-pic" src="<?= base_url('Assets/img/profil/' . $user['profile_pic']) ?>" alt="Foto Profil" class="profile-pic">
                <button type="button" class="edit-icon" id="edit-icon" title="Ubah Data">
                    <i class="fa fa-pencil"></i>
                </button>
                <button type="button" class="change-pic-icon" id="change-pic-icon" style="display: none;">
                    <i class="fa fa-image"></i>
                </button>
                <input type="file" id="profile-pic-input" name="profile_pic" title="Ubah Gambar" style="display: none;">
            </div>
            <h4 class="profile-name" id="profile-name"><?= $user['name'] ?></h4>
        </div>

        <div class="profil-body">
            <div class="profil-grid">
                <div class="profil-item wide">
                    <label>Nama Kandidat</label>
                    <div class="input-wrapper">
                        <span class="input-icon">@</span>
                        <input type="text" name="name" id="name" value="<?= $user['name'] ?>" readonly>
                    </div>
                </div>
                <div class="profil-item">
                    <label>Posisi Kandidat</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fa fa-code"></i></span>
                        <input type="text" name="position" id="position" value="<?= $user['position'] ?>" readonly>
                    </div>
                </div>
            </div>

            <div class="form-footer" style="display: none;" id="button-container">
                <button type="button" id="cancel-btn" class="btn btn-cancel">Batal</button>
                <button type="submit" id="save-btn" class="btn btn-save">Simpan</button>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editIcon = document.getElementById('edit-icon');
        const changePicIcon = document.getElementById('change-pic-icon');
        const profilePicInput = document.getElementById('profile-pic-input');
        const buttonContainer = document.getElementById('button-container');

        editIcon.addEventListener('click', function() {
            editIcon.style.display = 'none';
            changePicIcon.style.display = 'inline-block';
            profilePicInput.style.display = 'none'; 

            document.getElementById('name').readOnly = false;
            document.getElementById('position').readOnly = false;

            buttonContainer.style.display = 'flex';
        });

        document.getElementById('cancel-btn').addEventListener('click', function() {
            window.location.reload();
        });

        changePicIcon.addEventListener('click', function() {
            profilePicInput.click(); 
        });

        profilePicInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile-pic').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>

<?= $this->endSection() ?>