/**
 * Cyber-Tech GPU Store - Core JavaScript
 * SweetAlert2 Cyber Modals, Real-time Image Preview & UI Handlers
 */

// Image Fallback Handler
function handleImgError(img) {
  img.onerror = null;
  img.src = 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=600&q=80';
}

// SweetAlert2 Cyber Alert Preset
function cyberAlert(title, text, icon = 'success') {
  if (typeof Swal !== 'undefined') {
    return Swal.fire({
      title: title,
      text: text,
      icon: icon,
      customClass: {
        popup: 'cyber-swal',
        title: 'cyber-swal-title',
        confirmButton: 'btn-cyber-primary px-6 py-2 rounded-lg text-black'
      },
      buttonsStyling: false,
      background: '#0B1120',
      confirmButtonText: 'รับทราบ (OK)'
    });
  } else {
    alert(`${title}: ${text}`);
  }
}

// Confirm Delete with SweetAlert2
function confirmDelete(gpuId, gpuName) {
  if (typeof Swal !== 'undefined') {
    Swal.fire({
      title: 'ยืนยันการลบข้อมูล?',
      html: `คุณกำลังจะลบการ์ดจอ <span class="text-cyan-400 font-bold font-scifi">${gpuName}</span><br><small class="text-rose-400">การกระทำนี้ไม่สามารถย้อนกลับได้!</small>`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'ใช่, ลบข้อมูลทันที!',
      cancelButtonText: 'ยกเลิก',
      customClass: {
        popup: 'cyber-swal',
        title: 'cyber-swal-title',
        confirmButton: 'btn-cyber-danger px-5 py-2.5 rounded-lg text-white font-scifi font-bold mr-3',
        cancelButton: 'btn-cyber-outline px-5 py-2.5 rounded-lg text-slate-300 font-scifi'
      },
      buttonsStyling: false,
      background: '#0B1120'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = `delete_gpu.php?id=${gpuId}&confirm=1`;
      }
    });
  } else {
    if (confirm(`คุณต้องการลบ "${gpuName}" หรือไม่?`)) {
      window.location.href = `delete_gpu.php?id=${gpuId}&confirm=1`;
    }
  }
}

// Real-time Image Preview Binder
function initImagePreview(inputId, previewImgId) {
  const input = document.getElementById(inputId);
  const preview = document.getElementById(previewImgId);

  if (input && preview) {
    const updatePreview = () => {
      const url = input.value.trim();
      if (url) {
        preview.src = url;
        preview.onerror = () => handleImgError(preview);
      } else {
        preview.src = 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=600&q=80';
      }
    };

    input.addEventListener('input', updatePreview);
    input.addEventListener('change', updatePreview);
  }
}

// Quick Sample Image Preset Loader for Testing
function setSampleImage(url, inputId = 'image_url', previewId = 'imagePreview') {
  const input = document.getElementById(inputId);
  if (input) {
    input.value = url;
    input.dispatchEvent(new Event('input'));
  }
}

// Quick View Modal
function openQuickView(gpuData) {
  const modal = document.getElementById('quickViewModal');
  if (!modal) return;

  document.getElementById('qvModelName').innerText = gpuData.model_name || '';
  document.getElementById('qvBrand').innerText = (gpuData.brand_name || '') + ' • ' + (gpuData.chipset_model || '');
  document.getElementById('qvPrice').innerText = '฿' + Number(gpuData.price).toLocaleString();
  document.getElementById('qvStock').innerText = (gpuData.stock > 0 ? gpuData.stock + ' ชิ้น' : 'สินค้าหมด');
  document.getElementById('qvVram').innerText = gpuData.vram_gb + ' GB ' + (gpuData.memory_type || '');
  document.getElementById('qvClocks').innerText = (gpuData.core_clock || '-') + ' / ' + (gpuData.boost_clock || '-') + ' MHz';
  document.getElementById('qvBus').innerText = (gpuData.bus_width || '-') + '-bit';
  document.getElementById('qvTdp').innerText = (gpuData.tdp_watts || '-') + 'W';
  document.getElementById('qvDesc').innerText = gpuData.description || 'ไม่มีรายละเอียดเพิ่มเติม';
  
  const qvImg = document.getElementById('qvImg');
  if (qvImg) {
    qvImg.src = gpuData.image_url || 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=600&q=80';
    qvImg.onerror = () => handleImgError(qvImg);
  }

  modal.classList.remove('hidden');
  modal.classList.add('flex');
}

function closeQuickView() {
  const modal = document.getElementById('quickViewModal');
  if (modal) {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }
}

// Mobile Menu Toggle
function toggleMobileMenu() {
  const menu = document.getElementById('mobileMenu');
  if (menu) {
    menu.classList.toggle('hidden');
  }
}
