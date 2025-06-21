document.querySelectorAll('.view-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const fileId = this.dataset.fileId;
        const popup = document.getElementById('sharePopup');
        const shareLinkInput = document.getElementById('shareLink');
        const unshareBtn = document.getElementById('unshareBtn');

        shareLinkInput.value = window.location.origin + '/preview_shared.php?id=' + fileId;
        unshareBtn.dataset.fileId = fileId;

    
        document.getElementById('restrictedShareForm').style.display = 'none';
        document.getElementById('restrictedEmail').value = '';

        popup.style.display = 'flex';
    });
});

document.getElementById('closePopup').addEventListener('click', function(){
    document.getElementById('sharePopup').style.display = 'none';
});

document.getElementById('copyBtn').addEventListener('click', function(){
    const shareLinkInput = document.getElementById('shareLink');
    shareLinkInput.select();
    shareLinkInput.setSelectionRange(0, 99999); // Mobil uyumlu
    navigator.clipboard.writeText(shareLinkInput.value).then(() => {
        alert('Link kopyalandı!');
    });
});

document.getElementById('unshareBtn').addEventListener('click', function(){
    const fileId = this.dataset.fileId;

    fetch('unshare.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'file_id=' + encodeURIComponent(fileId)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Paylaşım kaldırıldı.');
            location.reload();
        } else {
            alert('Bir hata oluştu.');
        }
    })
    .catch(() => alert('Sunucu hatası'));
});

document.getElementById('restrictedBtn').addEventListener('click', function(){
    document.getElementById('restrictedShareForm').style.display = 'block';
});


document.getElementById('cancelRestrictedShare').addEventListener('click', function(){
    document.getElementById('restrictedShareForm').style.display = 'none';
    document.getElementById('restrictedEmail').value = '';
});


document.getElementById('confirmRestrictedShare').addEventListener('click', function(){
    const email = document.getElementById('restrictedEmail').value.trim();
    const fileId = document.getElementById('unshareBtn').dataset.fileId;

    if (!email || !email.includes('@')) {
        alert('Geçerli bir e-posta adresi giriniz.');
        return;
    }

    fetch('restricted_share.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `file_id=${encodeURIComponent(fileId)}&email=${encodeURIComponent(email)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Kısıtlı paylaşım başarıyla yapıldı.');
            document.getElementById('restrictedShareForm').style.display = 'none';
            document.getElementById('restrictedEmail').value = '';
        } else {
            alert('Kısıtlı paylaşım sırasında bir hata oluştu.');
        }
    })
    .catch(() => alert('Sunucu hatası'));
});
