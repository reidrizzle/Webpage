/**
 * Cyber-Tech GPU Store - Core JavaScript
 * SweetAlert2 Cyber Modals, Real-time Image Preview, Buying & UI Handlers
 */

// Image Fallback Handler
function handleImgError(img) {
  img.onerror = null;
  img.src = 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products103815_800.jpg';
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
        confirmButton: 'btn-cyber-primary px-6 py-2 rounded-lg text-black font-scifi'
      },
      buttonsStyling: false,
      background: '#0B1120',
      confirmButtonText: 'รับทราบ (OK)'
    });
  } else {
    alert(`${title}: ${text}`);
  }
}

// Confirm Delete with SweetAlert2 (Admin Only)
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

// Instant Buy GPU Confirmation
function confirmBuy(gpuId, gpuName, price) {
  if (!window.isUserLoggedIn) {
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: 'กรุณาเข้าสู่ระบบ',
        html: 'คุณต้องเข้าสู่ระบบก่อนสั่งซื้อการ์ดจอ<br><small class="text-amber-400 font-bold">สมาชิกใหม่รับวงเงินจำลอง ฿9,999,999 ทันทีสำหรับการทดสอบ!</small>',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'เข้าสู่ระบบตอนนี้',
        cancelButtonText: 'ไว้ทีหลัง',
        customClass: {
          popup: 'cyber-swal',
          title: 'cyber-swal-title',
          confirmButton: 'btn-cyber-primary px-5 py-2.5 rounded-lg text-black font-scifi font-bold mr-3',
          cancelButton: 'btn-cyber-outline px-5 py-2.5 rounded-lg text-slate-300 font-scifi'
        },
        buttonsStyling: false,
        background: '#0B1120'
      }).then((res) => {
        if (res.isConfirmed) {
          window.location.href = 'login.php';
        }
      });
    } else {
      window.location.href = 'login.php';
    }
    return;
  }

  const wallet = Number(window.currentUserWallet !== undefined ? window.currentUserWallet : 9999999);
  const numPrice = Number(price);

  if (wallet < numPrice) {
    cyberAlert('ยอดเงินไม่เพียงพอ', 'ยอดเงินในกระเป๋าของคุณไม่เพียงพอสำหรับการสั่งซื้อการ์ดจอนี้ สามารถกดเติมเงินฟรีได้ที่หน้าโปรไฟล์ของคุณ', 'error');
    return;
  }

  const remaining = wallet - numPrice;
  if (typeof Swal !== 'undefined') {
    Swal.fire({
      title: 'ยืนยันการสั่งซื้อการ์ดจอ',
      html: `
        <div class="text-left text-xs space-y-2 p-3.5 rounded-xl bg-slate-900/90 border border-slate-800 font-mono mt-3">
          <p class="text-slate-300"><strong>รุ่นการ์ดจอ:</strong> <span class="text-white font-bold">${gpuName}</span></p>
          <p class="text-slate-300"><strong>ราคา:</strong> <span class="text-emerald-400 font-bold text-sm">฿${numPrice.toLocaleString(undefined, {minimumFractionDigits: 2})}</span></p>
          <p class="text-slate-400"><strong>ยอดเงินในวอลเล็ตปัจจุบัน:</strong> ฿${wallet.toLocaleString(undefined, {minimumFractionDigits: 2})}</p>
          <p class="text-cyan-400 pt-2 border-t border-slate-800 font-bold"><strong>ยอดเงินคงเหลือหลังสั่งซื้อ:</strong> ฿${remaining.toLocaleString(undefined, {minimumFractionDigits: 2})}</p>
        </div>
      `,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'ยืนยันสั่งซื้อและหักเงินทันที!',
      cancelButtonText: 'ยกเลิก',
      customClass: {
        popup: 'cyber-swal',
        title: 'cyber-swal-title',
        confirmButton: 'btn-cyber-nvidia px-5 py-2.5 rounded-lg text-black font-scifi font-bold mr-3',
        cancelButton: 'btn-cyber-outline px-5 py-2.5 rounded-lg text-slate-300 font-scifi'
      },
      buttonsStyling: false,
      background: '#0B1120'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = `buy_gpu.php?gpu_id=${gpuId}`;
      }
    });
  } else {
    if (confirm(`ยืนยันการสั่งซื้อ "${gpuName}" ในราคา ฿${numPrice.toLocaleString()} หรือไม่?`)) {
      window.location.href = `buy_gpu.php?gpu_id=${gpuId}`;
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

// Global GPU Catalog Registry for safe Quick View lookup
window.gpuCatalog = window.gpuCatalog || {};

function registerGpuCatalog(items) {
  if (Array.isArray(items)) {
    items.forEach(function(item) {
      if (item && item.id) {
        window.gpuCatalog[item.id] = item;
      }
    });
  } else if (items && typeof items === 'object') {
    Object.assign(window.gpuCatalog, items);
  }
}

function openQuickViewById(id) {
  openQuickView(id);
}

// Quick View Modal (supports HTMLElement, GPU Object, or GPU ID)
function openQuickView(input) {
  let gpuData = null;

  // 1. If an HTML element (the button clicked) is passed
  if (input && (input instanceof HTMLElement || input.nodeType === 1)) {
    const raw = input.getAttribute('data-gpu');
    if (raw) {
      try {
        gpuData = JSON.parse(raw);
      } catch (e) {
        console.error('Error parsing data-gpu attribute:', e);
      }
    }
    if (!gpuData) {
      const elId = input.getAttribute('data-gpu-id') || input.getAttribute('data-id');
      if (elId && window.gpuCatalog && window.gpuCatalog[elId]) {
        gpuData = window.gpuCatalog[elId];
      }
    }
  } 
  // 2. If ID was passed (number or string)
  else if (typeof input === 'number' || typeof input === 'string') {
    if (window.gpuCatalog && window.gpuCatalog[input]) {
      gpuData = window.gpuCatalog[input];
    } else {
      const el = document.querySelector('[data-gpu-id="' + input + '"]');
      if (el && el.getAttribute('data-gpu')) {
        try {
          gpuData = JSON.parse(el.getAttribute('data-gpu'));
        } catch (e) {}
      }
    }
  } 
  // 3. If full GPU object was passed
  else if (input && typeof input === 'object') {
    gpuData = input;
  }

  if (!gpuData) {
    console.warn('openQuickView: No GPU data found for input', input);
    return;
  }

  const modal = document.getElementById('quickViewModal');
  if (!modal) {
    console.error('quickViewModal element not found in DOM');
    return;
  }

  const safeSetText = (id, text) => {
    const el = document.getElementById(id);
    if (el) el.innerText = (text !== undefined && text !== null) ? text : '';
  };

  safeSetText('qvModelName', gpuData.model_name || '');
  safeSetText('qvBrand', (gpuData.brand_name || '') + (gpuData.chipset_model ? ' • ' + gpuData.chipset_model : ''));
  safeSetText('qvPrice', '฿' + Number(gpuData.price || 0).toLocaleString());
  safeSetText('qvStock', (Number(gpuData.stock) > 0 ? 'คงเหลือ ' + gpuData.stock + ' ชิ้น' : 'สินค้าหมด'));
  safeSetText('qvVram', (gpuData.vram_gb ? gpuData.vram_gb + ' GB ' : '') + (gpuData.memory_type || ''));
  safeSetText('qvClocks', (gpuData.core_clock || '-') + ' / ' + (gpuData.boost_clock || '-') + ' MHz');
  safeSetText('qvBus', (gpuData.bus_width || '-') + '-bit');
  safeSetText('qvTdp', (gpuData.tdp_watts || '-') + 'W');
  safeSetText('qvDesc', gpuData.description || 'ไม่มีรายละเอียดเพิ่มเติม');
  
  const qvImg = document.getElementById('qvImg');
  if (qvImg) {
    qvImg.src = gpuData.image_url || 'assets/images/gpus/asus_rog_rtx_4090.jpg';
    qvImg.onerror = function() {
      this.src = 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products103815_800.jpg';
    };
  }

  // Hook Quick View Buy Button
  const qvBuyBtn = document.getElementById('qvBuyBtn');
  if (qvBuyBtn) {
    if (Number(gpuData.stock) > 0) {
      qvBuyBtn.disabled = false;
      qvBuyBtn.className = "btn-cyber-primary px-4 py-2 rounded-xl text-xs font-bold font-scifi";
      qvBuyBtn.innerHTML = '<i class="fa-solid fa-cart-shopping mr-1.5"></i> สั่งซื้อทันที';
      qvBuyBtn.onclick = function() {
        closeQuickView();
        if (typeof confirmBuy === 'function') {
          confirmBuy(gpuData.id, gpuData.model_name, gpuData.price);
        }
      };
    } else {
      qvBuyBtn.disabled = true;
      qvBuyBtn.className = "px-4 py-2 rounded-xl text-xs font-bold font-scifi bg-slate-800 text-slate-500 cursor-not-allowed";
      qvBuyBtn.innerHTML = 'สินค้าหมด';
      qvBuyBtn.onclick = null;
    }
  }

  modal.classList.remove('hidden');
  modal.classList.add('flex');
  modal.style.display = 'flex';
}

function closeQuickView() {
  const modal = document.getElementById('quickViewModal');
  if (modal) {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    modal.style.display = 'none';
  }
}

// Global click delegation for any eye icon / Quick View button
document.addEventListener('click', function(e) {
  const btn = e.target.closest('button[data-gpu-id]');
  if (btn && btn.title && (btn.title.includes('ดูสเปคด่วน') || btn.title.includes('ดูตัวอย่าง'))) {
    openQuickView(btn);
  }
});

// ESC Key closes QuickView
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape' || e.keyCode === 27) {
    closeQuickView();
  }
});

// Mobile Menu Toggle
function toggleMobileMenu() {
  const menu = document.getElementById('mobileMenu');
  if (menu) {
    menu.classList.toggle('hidden');
  }
}
