// public/js/app.js
// Bind UI interactions, load data via API, and render charts/tables.

(function(){
  document.addEventListener('click', (e) => {
    const openBtn = e.target.closest('[data-open-modal]');
    if(openBtn){
      const sel = openBtn.getAttribute('data-open-modal');
      const modal = document.querySelector(sel);
      if(modal){ modal.setAttribute('aria-hidden','false'); }
    }
    if(e.target.matches('[data-close-modal]') || e.target.closest('[data-close-modal]')){
      const modal = e.target.closest('.modal');
      if(modal){ modal.setAttribute('aria-hidden','true'); }
    }
  });

  // DASHBOARD
  if(document.getElementById('yearHistoryChart')){
    API.getDashboard().then(d => {
      Charts.yearHistory(document.getElementById('yearHistoryChart'), d.yearHistory);
      Charts.weekActivity(
        document.getElementById('weekActivityChart'),
        d.weekActivity.labels,
        d.weekActivity.booked,
        d.weekActivity.canceled
      );
      Charts.seatPie(
        document.getElementById('seatPieChart'),
        d.seatToday.labels,
        d.seatToday.values
      );
    });
  }

  // TICKETS PAGE
  const ticketTable = document.getElementById('ticketTable');
  if(ticketTable){
    Promise.all([API.getTickets(), API.getVehicles(), API.getCities()]).then(([tickets, vehicles, cities]) => {
      // fill table
      const tbody = ticketTable.querySelector('tbody');
      tbody.innerHTML = tickets.map(t => `
        <tr>
          <td>${t.vehicle}</td>
          <td>${t.type}</td>
          <td>${t.plate}</td>
          <td>${t.seats}</td>
          <td>${t.time}</td>
          <td>${t.date}</td>
          <td>${t.route}</td>
          <td>${formatCurrency(t.price)}</td>
          <td>
            <button class="icon-btn" title="Sửa"><i class="ri-pencil-line"></i></button>
            <button class="icon-btn" title="Xoá"><i class="ri-delete-bin-6-line"></i></button>
          </td>
        </tr>`).join('');

      // fill modal selects
      const form = document.getElementById('ticketForm');
      const vehicleSel = form.querySelector('select[name="vehicleId"]');
      vehicleSel.innerHTML = vehicles.map(v => `<option value="${v.id}">${v.name}</option>`).join('');
      const fromSel = form.querySelector('select[name="fromCity"]');
      const toSel = form.querySelector('select[name="toCity"]');
      fromSel.innerHTML = toSel.innerHTML = cities.map(c => `<option>${c}</option>`).join('');

      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const payload = Object.fromEntries(new FormData(form).entries());
        payload.price = Number(payload.price);
        const res = await API.createTicket(payload);
        if(res && res.ok){
          alert('Tạo vé thành công');
          document.getElementById('ticketModal').setAttribute('aria-hidden','true');
        } else {
          alert(res && res.error ? res.error : 'Tạo vé chưa được hỗ trợ trong phiên bản này.');
        }
      });
    });
  }

  // TRANSFERS PAGE
  const transferGroups = document.getElementById('transferGroups');
  if(transferGroups){
    function renderGroups(groups){
      const html = (Array.isArray(groups) ? groups : []).map(g => {
        const stops = (g.stops || []);
        const items = stops.map((s) => `
          <li class="chip" data-stop-id="${s.id}">
            <span>${s.name || s.address || ('#'+s.id)}</span>
            <div class="chip-actions">
              <button class="icon-btn edit-stop" title="Sửa" data-id="${s.id}"><i class="ri-pencil-line"></i></button>
              <button class="icon-btn delete-stop" title="Xoá" data-id="${s.id}"><i class="ri-delete-bin-6-line"></i></button>
            </div>
          </li>`).join('');
        return `
          <div class="group-card">
            <div class="row-between">
              <div class="group-title">${g.city}</div>
              <button class="btn add-stop" data-city="${g.city}"><i class="ri-add-circle-line"></i> Thêm</button>
            </div>
            <ul class="chip-list">${items || '<li class="muted">(Chưa có điểm trung chuyển)</li>'}</ul>
          </div>`;
      }).join('');
      transferGroups.innerHTML = html || '<div class="muted">Chưa có dữ liệu trung chuyển.</div>';
    }

    async function reload(){
      const groups = await API.getTransfers();
      renderGroups(groups);
    }

    Promise.all([API.getTransfers(), API.getCities()]).then(([groups, cities]) => {
      // Hợp nhất: tạo group cho tất cả city, kể cả khi chưa có stop
      const mapByCity = new Map((groups||[]).map(g => [String(g.city).toLowerCase(), g]));
      const merged = (cities||[]).map(c => {
        const key = String(c).toLowerCase();
        return mapByCity.get(key) || { city: c, stops: [] };
      });
      renderGroups(merged);

      // create form
      const form = document.getElementById('transferForm');
      const citySel = form.querySelector('select[name="city"]');
      citySel.innerHTML = cities.map(c => `<option>${c}</option>`).join('');
      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const payload = Object.fromEntries(new FormData(form).entries());
        const res = await API.createTransfer(payload);
        if(res && res.ok){
          document.getElementById('transferModal').setAttribute('aria-hidden','true');
          await reload();
        } else {
          alert(res && res.error || 'Tạo thất bại');
        }
      });

      // edit / delete / quick-add handlers
      const editForm = document.getElementById('editStopForm');
      document.addEventListener('click', async (e) => {
        const del = e.target.closest('.delete-stop');
        const edit = e.target.closest('.edit-stop');
        const add = e.target.closest('.add-stop');
        if(del){
          const id = del.getAttribute('data-id');
          if(id && confirm('Xoá điểm trung chuyển này?')){
            const rs = await API.deleteTransfer(id);
            if(rs && rs.ok){ await reload(); } else { alert(rs && rs.error || 'Xoá thất bại'); }
          }
        } else if(edit){
          const id = edit.getAttribute('data-id');
          editForm.id.value = id;
          // Prefill via DOM text for simplicity
          const chip = edit.closest('.chip');
          const nameText = chip?.querySelector('span')?.textContent || '';
          editForm.name.value = nameText;
          editForm.address.value = nameText;
          document.getElementById('editStopModal').setAttribute('aria-hidden','true');
        } else if(add){
          const city = add.getAttribute('data-city');
          if(city){
            citySel.value = city;
            document.getElementById('transferModal').setAttribute('aria-hidden','false');
          }
        }
      });

      editForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(editForm);
        const id = fd.get('id');
        const body = { name: fd.get('name'), address: fd.get('address') };
        const rs = await API.updateTransfer(id, body);
        if(rs && rs.ok){
          document.getElementById('editStopModal').setAttribute('aria-hidden','true');
          await reload();
        } else {
          alert(rs && rs.error || 'Cập nhật thất bại');
        }
      });
    });

  }

  // VEHICLES PAGE
  const vehicleTable = document.getElementById('vehicleTable');
  if(vehicleTable){
    function renderVehicles(list){
      const tbody = vehicleTable.querySelector('tbody');
      tbody.innerHTML = (list||[]).map((v, idx) => `
        <tr data-id="${v.id}" data-name="${v.name}" data-type="${v.type}">
          <td>${idx+1}</td>
          <td>${v.name}</td>
          <td>${v.type}</td>
          <td>${v.seats}</td>
          <td>
            <button class="icon-btn edit-vehicle" title="Sửa"><i class="ri-pencil-line"></i></button>
            <button class="icon-btn delete-vehicle" title="Xoá"><i class="ri-delete-bin-6-line"></i></button>
          </td>
        </tr>`).join('');
    }

    API.getVehicles().then(renderVehicles);

    // Edit handler (open modal, prefill)
    const editModal = document.getElementById('editVehicleModal');
    const editForm = document.getElementById('editVehicleForm');
    vehicleTable.addEventListener('click', async (e) => {
      const btn = e.target.closest('.edit-vehicle');
      if(!btn) return;
      const tr = btn.closest('tr');
      const id = tr.getAttribute('data-id');
      editForm.id.value = id;
      // Lấy chi tiết từ API để điền đầy đủ (đặc biệt license_plate)
      const detail = await API.getVehicle(id);
      editForm.name.value = detail?.name || tr.getAttribute('data-name') || '';
      editForm.vehicle_type.value = detail?.vehicle_type || tr.getAttribute('data-type') || 'bus';
      editForm.license_plate.value = detail?.license_plate || '';
      // Hiển thị danh sách toa hiện có nếu là tàu
      const editTrainSec = document.getElementById('editTrainSection');
      const coachTbody = document.querySelector('#existingCoachTable tbody');
      if((detail?.vehicle_type || '').toLowerCase() === 'train' && Array.isArray(detail?.coaches)){
        editTrainSec.style.display = '';
        coachTbody.innerHTML = detail.coaches.map(c => `
          <tr>
            <td>${c.identifier}</td>
            <td>${c.coach_type === 'seat_VIP' ? 'Ghế VIP' : 'Ghế mềm'}</td>
            <td>${c.total_seats}</td>
          </tr>`).join('');
      } else {
        editTrainSec.style.display = 'none';
        coachTbody.innerHTML = '';
      }
      editModal.setAttribute('aria-hidden','false');
    });

    editForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(editForm);
      const id = fd.get('id');
      const payload = {
        name: fd.get('name'),
        vehicle_type: fd.get('vehicle_type'),
        license_plate: fd.get('license_plate') || null,
      };
      const rs = await API.updateVehicle(id, payload);
      if(rs && rs.ok){
        editModal.setAttribute('aria-hidden','true');
        const list = await API.getVehicles();
        renderVehicles(list);
      } else {
        alert(rs && rs.error || 'Cập nhật phương tiện thất bại');
      }
    });

    // Form logic for create (bus/train)
    const form = document.getElementById('vehicleForm');
    const vehicleTypeSel = form.querySelector('select[name="vehicle_type"]');
    const busFields = document.getElementById('busFields');
    const trainFields = document.getElementById('trainFields');
    const coachTableBody = document.querySelector('#coachTable tbody');
    const addCoachBtn = document.getElementById('addCoachBtn');

    function syncTypeVisibility(){
      const t = vehicleTypeSel.value;
      busFields.style.display = (t === 'bus') ? '' : 'none';
      trainFields.style.display = (t === 'train') ? '' : 'none';
    }
    vehicleTypeSel.addEventListener('change', syncTypeVisibility);
    syncTypeVisibility();

    function addCoachRow(row={ coach_type:'seat_soft', quantity:1 }){
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>
          <select class="coach_type">
            <option value="seat_soft" ${row.coach_type==='seat_soft'?'selected':''}>Ghế mềm (40)</option>
            <option value="seat_VIP" ${row.coach_type==='seat_VIP'?'selected':''}>Ghế VIP (24)</option>
          </select>
        </td>
        <td>
          <input type="text" class="total_seats" value="${row.coach_type==='seat_VIP'?24:40}" readonly />
        </td>
        <td><input type="number" class="quantity" min="1" value="${row.quantity}" /></td>
        <td><button type="button" class="icon-btn remove-coach" title="Xoá"><i class="ri-delete-bin-6-line"></i></button></td>
      `;
      coachTableBody.appendChild(tr);
      // cập nhật total_seats khi đổi loại
      const sel = tr.querySelector('.coach_type');
      const seatInput = tr.querySelector('.total_seats');
      sel.addEventListener('change', () => { seatInput.value = sel.value==='seat_VIP' ? 24 : 40; });
    }
    if(addCoachBtn){ addCoachBtn.addEventListener('click', () => addCoachRow()); }
    coachTableBody?.addEventListener('click', (e) => {
      if(e.target.closest('.remove-coach')){
        const tr = e.target.closest('tr');
        tr && tr.remove();
      }
    });

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(form);
      const vehicle_type = fd.get('vehicle_type');
      const name = (fd.get('name')||'').toString().trim();
      const license_plate = (fd.get('license_plate')||'').toString().trim() || null;

      let payload;
      if(vehicle_type === 'bus'){
        const coach_type = fd.get('bus_coach_type');
        const total_seats = Number(fd.get('bus_total_seats')||0);
        payload = { name, vehicle_type, license_plate, coach: { coach_type, total_seats } };
      } else {
        // train: collect coach rows
        const rows = Array.from(coachTableBody?.querySelectorAll('tr') || []);
        const coaches = rows.map(r => ({
          coach_type: r.querySelector('.coach_type').value,
          quantity: Number(r.querySelector('.quantity').value||1)
        })).filter(c => c.quantity>0);
        if(!coaches.length){ return alert('Vui lòng thêm ít nhất 1 toa cho tàu hoả'); }
        payload = { name, vehicle_type, license_plate: null, coaches };
      }

      const res = await API.createVehicle(payload);
      if(res && res.ok){
        alert('Tạo phương tiện thành công');
        document.getElementById('vehicleModal').setAttribute('aria-hidden','true');
        const list = await API.getVehicles();
        renderVehicles(list);
      } else {
        alert(res && res.error ? res.error : 'Không thể tạo phương tiện');
      }
    });

    // Delete handler
    vehicleTable.addEventListener('click', async (e) => {
      const btn = e.target.closest('.delete-vehicle');
      if(!btn) return;
      const tr = btn.closest('tr');
      const id = tr && tr.getAttribute('data-id');
      if(id && confirm('Bạn chắc chắn xoá phương tiện này?')){
        const res = await API.deleteVehicle(id);
        if(res && res.ok){
          const list = await API.getVehicles();
          renderVehicles(list);
        } else {
          alert(res && res.error || 'Xoá thất bại');
        }
      }
    });
  }
})();

function formatCurrency(value){
  return (value || 0).toLocaleString('vi-VN', { style: 'currency', currency: 'VND' });
}
