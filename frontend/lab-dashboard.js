const API_BASE = 'http://localhost:8000';

window.addEventListener('DOMContentLoaded', async () => {
    await checkAuth();
    await loadPatients();
});

async function checkAuth() {
    try {
        console.log('Checking authentication...');
        const response = await fetch(`${API_BASE}/api/auth/check`, {
            credentials: 'include'
        });
        
        console.log('Auth response status:', response.status);
        const data = await response.json();
        console.log('Auth response data:', data);
        
        if (!data.authenticated || data.user_type !== 'lab') {
            console.log('Not authenticated or wrong user type');
            console.log('Authenticated:', data.authenticated);
            console.log('User type:', data.user_type);
            alert('Session expired or invalid. Redirecting to login...');
            window.location.href = 'lab-login.html';
            return;
        }
        
        console.log('Authentication successful!');
        document.getElementById('labName').textContent = data.username || 'Lab User';
    } catch (error) {
        console.error('Auth check failed:', error);
        alert('Error checking authentication: ' + error.message);
        window.location.href = 'lab-login.html';
    }
}

async function logout() {
    try {
        await fetch(`${API_BASE}/api/auth/logout`, {
            method: 'POST',
            credentials: 'include'
        });
        
        window.location.href = 'lab-login.html';
    } catch (error) {
        console.error('Logout failed:', error);
    }
}

function showTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('border-blue-500', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById(`${tabName}Section`).classList.remove('hidden');
    
    const activeBtn = document.getElementById(`${tabName}Tab`);
    activeBtn.classList.add('border-blue-500', 'text-blue-600');
    activeBtn.classList.remove('border-transparent', 'text-gray-500');
    
    if (tabName === 'addReport') {
        loadPatientsForReport();
    }
}

async function loadPatients() {
    try {
        const response = await fetch(`${API_BASE}/api/lab/patients`, {
            credentials: 'include'
        });
        
        const data = await response.json();
        
        if (data.success) {
            displayPatients(data.patients);
        }
    } catch (error) {
        console.error('Failed to load patients:', error);
    }
}

function displayPatients(patients) {
    const tbody = document.getElementById('patientsTableBody');
    
    if (patients.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No patients registered yet</td></tr>';
        return;
    }
    
    tbody.innerHTML = patients.map(patient => `
        <tr>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${patient.Patient_ID}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${patient.F_name} ${patient.L_name}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${patient.Email}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${patient.DOB}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${patient.Gender}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                <button onclick="viewPatientReports(${patient.Patient_ID})" class="text-blue-600 hover:text-blue-900">View Reports</button>
            </td>
        </tr>
    `).join('');
}

async function loadPatientsForReport() {
    try {
        const response = await fetch(`${API_BASE}/api/lab/patients`, {
            credentials: 'include'
        });
        
        const data = await response.json();
        
        if (data.success) {
            const select = document.getElementById('reportPatientSelect');
            select.innerHTML = '<option value="">Select Patient</option>' + 
                data.patients.map(p => `<option value="${p.Patient_ID}">${p.F_name} ${p.L_name} (${p.Email})</option>`).join('');
        }
    } catch (error) {
        console.error('Failed to load patients for report:', error);
    }
}

document.getElementById('addPatientForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    
    if (data.phone) {
        const phoneRegex = /^0[0-9]{9}$/;
        if (!phoneRegex.test(data.phone)) {
            alert('Please enter a valid Sri Lankan phone number (10 digits starting with 0)');
            return;
        }
    }
    
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
    if (!passwordRegex.test(data.password)) {
        alert('Password must be at least 8 characters and contain uppercase, lowercase, and a number');
        return;
    }
    
    if (data.password !== data.confirm_password) {
        alert('Passwords do not match!');
        return;
    }
    
    delete data.confirm_password;
    
    try {
        const response = await fetch(`${API_BASE}/api/lab/patients`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccessModal('Patient Registered', 'Patient has been successfully registered!');
            e.target.reset();
            await loadPatients();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Failed to register patient:', error);
        alert('Failed to register patient. Please try again.');
    }
});

document.getElementById('addReportForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    
    Object.keys(data).forEach(key => {
        if (key !== 'patient_id') {
            data[key] = parseFloat(data[key]);
        }
    });
    
    try {
        const button = e.target.querySelector('button[type="submit"]');
        button.disabled = true;
        button.textContent = 'Processing... Please wait';
        
        console.log('Sending report data:', data);
        
        const response = await fetch(`${API_BASE}/api/lab/reports`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify(data)
        });
        
        console.log('Response status:', response.status);
        console.log('Response headers:', [...response.headers.entries()]);
        
        const rawText = await response.text();
        console.log('Raw response:', rawText);
        console.log('First 1000 chars:', rawText.substring(0, 1000));
        
        button.disabled = false;
        button.textContent = 'Create Report & Get Predictions';
        
        try {
            const result = JSON.parse(rawText);
            console.log('Parsed result:', result);
            
            if (result.success) {
                showPredictionResults(result.predictions);
                e.target.reset();
            } else {
                alert('Error: ' + result.message);
            }
        } catch (parseError) {
            console.error('JSON parse error:', parseError);
            alert('Server returned non-JSON response. Check console for details.');
        }
    } catch (error) {
        console.error('Failed to create report:', error);
        alert('Failed to create report. Please try again.');
        const button = e.target.querySelector('button[type="submit"]');
        button.disabled = false;
        button.textContent = 'Create Report & Get Predictions';
    }
});

function showPredictionResults(predictions) {
    let message = 'Report created successfully!\n\nPrediction Results:\n\n';
    
    for (const [key, value] of Object.entries(predictions)) {
        message += `${key}: ${value.value.toFixed(2)} - ${value.status}\n`;
    }
    
    alert(message);
}

function showSuccessModal(title, message) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMessage').textContent = message;
    document.getElementById('successModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('successModal').classList.add('hidden');
}

function viewPatientReports(patientId) {
    window.location.href = `patient-reports.html?patient_id=${patientId}`;
}
