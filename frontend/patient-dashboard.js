const API_BASE = 'http://localhost:8000';

window.addEventListener('DOMContentLoaded', async () => {
    await checkAuth();
    await loadProfile();
});

async function checkAuth() {
    try {
        const response = await fetch(`${API_BASE}/api/auth/check`, {
            credentials: 'include'
        });
        
        const data = await response.json();
        
        if (!data.authenticated || data.user_type !== 'patient') {
            window.location.href = 'patient-login.html';
            return;
        }
        
        document.getElementById('patientName').textContent = data.username || 'Patient';
    } catch (error) {
        console.error('Auth check failed:', error);
        window.location.href = 'patient-login.html';
    }
}

async function logout() {
    try {
        await fetch(`${API_BASE}/api/auth/logout`, {
            method: 'POST',
            credentials: 'include'
        });
        
        window.location.href = 'patient-login.html';
    } catch (error) {
        console.error('Logout failed:', error);
    }
}

function showTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('border-green-500', 'text-green-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById(`${tabName}Section`).classList.remove('hidden');
    
    const activeBtn = document.getElementById(`${tabName}Tab`);
    activeBtn.classList.add('border-green-500', 'text-green-600');
    activeBtn.classList.remove('border-transparent', 'text-gray-500');
    
    if (tabName === 'reports') {
        loadReports();
    } else if (tabName === 'predictions') {
        loadPredictions();
    }
}

async function loadProfile() {
    try {
        const response = await fetch(`${API_BASE}/api/patient/profile`, {
            credentials: 'include'
        });
        
        const data = await response.json();
        
        if (data.success) {
            displayProfile(data.patient);
        }
    } catch (error) {
        console.error('Failed to load profile:', error);
    }
}

function displayProfile(patient) {
    const profileInfo = document.getElementById('profileInfo');
    
    profileInfo.innerHTML = `
        <div>
            <label class="block text-sm font-medium text-gray-500">Patient ID</label>
            <p class="mt-1 text-lg">${patient.Patient_ID}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-500">Full Name</label>
            <p class="mt-1 text-lg">${patient.F_name} ${patient.L_name}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-500">Email</label>
            <p class="mt-1 text-lg">${patient.Email}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-500">Date of Birth</label>
            <p class="mt-1 text-lg">${patient.DOB}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-500">Gender</label>
            <p class="mt-1 text-lg">${patient.Gender}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-500">Address</label>
            <p class="mt-1 text-lg">${patient.Address || 'N/A'}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-500">Username</label>
            <p class="mt-1 text-lg">${patient.Username}</p>
        </div>
    `;
}

async function loadReports() {
    try {
        const response = await fetch(`${API_BASE}/api/patient/reports`, {
            credentials: 'include'
        });
        
        const data = await response.json();
        
        if (data.success) {
            displayReports(data.reports);
        }
    } catch (error) {
        console.error('Failed to load reports:', error);
    }
}

function displayReports(reports) {
    const container = document.getElementById('reportsContainer');
    
    if (reports.length === 0) {
        container.innerHTML = '<p class="p-6 text-center text-gray-500">No reports available</p>';
        return;
    }
    
    container.innerHTML = reports.map(report => `
        <div class="border-b last:border-b-0 p-6 hover:bg-gray-50">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold">Report #${report.Report_ID}</h3>
                    <p class="text-sm text-gray-500 mt-1">Date: ${new Date(report.created_at).toLocaleDateString()}</p>
                    
                    <div class="mt-4 grid grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Hemoglobin:</span>
                            <span class="font-medium ml-2">${report.Hb}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">WBC:</span>
                            <span class="font-medium ml-2">${report.WBC}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Glucose (FPG):</span>
                            <span class="font-medium ml-2">${report.FPG}</span>
                        </div>
                    </div>
                    
                    ${report.prediction ? `
                        <div class="mt-3 p-3 bg-blue-50 rounded">
                            <p class="text-sm font-medium text-blue-900">Predictions Available</p>
                            <div class="mt-2 grid grid-cols-3 gap-2 text-xs">
                                <div>
                                    <span class="text-gray-600">Ferritin:</span>
                                    <span class="ml-1 ${report.prediction.ferritin_abnormal ? 'text-red-600 font-semibold' : 'text-green-600'}">${report.prediction.Ferritin}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">B12:</span>
                                    <span class="ml-1 ${report.prediction.b12_abnormal ? 'text-red-600 font-semibold' : 'text-green-600'}">${report.prediction.Vitamin_B12}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">HbA1c:</span>
                                    <span class="ml-1 ${report.prediction.hba1c_abnormal ? 'text-red-600 font-semibold' : 'text-green-600'}">${report.prediction.HbA1c}</span>
                                </div>
                            </div>
                        </div>
                    ` : ''}
                </div>
                <button onclick="viewReportDetails(${report.Report_ID})" class="ml-4 text-green-600 hover:text-green-700 font-medium">
                    View Full Report
                </button>
            </div>
        </div>
    `).join('');
}

async function viewReportDetails(reportId) {
    try {
        const response = await fetch(`${API_BASE}/api/patient/reports/${reportId}`, {
            credentials: 'include'
        });
        
        const data = await response.json();
        
        if (data.success) {
            showReportModal(data.report, data.prediction);
        }
    } catch (error) {
        console.error('Failed to load report details:', error);
    }
}

function showReportModal(report, prediction) {
    const detailsDiv = document.getElementById('reportDetails');
    
    detailsDiv.innerHTML = `
        <div class="space-y-6">
            <div class="bg-gray-50 p-4 rounded">
                <h4 class="font-semibold mb-2">Report Information</h4>
                <p class="text-sm text-gray-600">Report ID: ${report.Report_ID}</p>
                <p class="text-sm text-gray-600">Date: ${new Date(report.created_at).toLocaleString()}</p>
            </div>
            
            <div>
                <h4 class="font-semibold mb-3">Blood Count Parameters</h4>
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-gray-500">Hemoglobin (Hb)</p>
                        <p class="text-lg font-semibold">${report.Hb}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-gray-500">Hematocrit (HCT)</p>
                        <p class="text-lg font-semibold">${report.HCT}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-gray-500">RBC</p>
                        <p class="text-lg font-semibold">${report.RBC}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-gray-500">MCV</p>
                        <p class="text-lg font-semibold">${report.MCV}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-gray-500">MCH</p>
                        <p class="text-lg font-semibold">${report.MCH}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-gray-500">MCHC</p>
                        <p class="text-lg font-semibold">${report.MCHC}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-gray-500">WBC</p>
                        <p class="text-lg font-semibold">${report.WBC}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-gray-500">Neutrophils</p>
                        <p class="text-lg font-semibold">${report.Neutrophils}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-gray-500">Lymphocytes</p>
                        <p class="text-lg font-semibold">${report.Lymphocytes}</p>
                    </div>
                </div>
            </div>
            
            <div>
                <h4 class="font-semibold mb-3">Liver Function Tests</h4>
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-gray-500">ALT</p>
                        <p class="text-lg font-semibold">${report.ALT}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-gray-500">AST</p>
                        <p class="text-lg font-semibold">${report.AST}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-gray-500">GGT</p>
                        <p class="text-lg font-semibold">${report.GGT}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-gray-500">Albumin</p>
                        <p class="text-lg font-semibold">${report.Albumin}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-gray-500">ALP</p>
                        <p class="text-lg font-semibold">${report.ALP}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-gray-500">Bilirubin Total</p>
                        <p class="text-lg font-semibold">${report.Bilirubin_Total}</p>
                    </div>
                </div>
            </div>
            
            <div>
                <h4 class="font-semibold mb-3">Kidney Function & Metabolic</h4>
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-gray-500">Urea</p>
                        <p class="text-lg font-semibold">${report.Urea}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-gray-500">Creatinine</p>
                        <p class="text-lg font-semibold">${report.Creatinine}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-gray-500">eGFR</p>
                        <p class="text-lg font-semibold">${report.eGFR}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-gray-500">FPG (Glucose)</p>
                        <p class="text-lg font-semibold">${report.FPG}</p>
                    </div>
                </div>
            </div>
            
            <div>
                <h4 class="font-semibold mb-3">Lipid Profile</h4>
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-gray-500">Triglycerides</p>
                        <p class="text-lg font-semibold">${report.Triglycerides}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-gray-500">Total Cholesterol</p>
                        <p class="text-lg font-semibold">${report.Cholesterol_Total}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-gray-500">HDL</p>
                        <p class="text-lg font-semibold">${report.HDL}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-gray-500">LDL</p>
                        <p class="text-lg font-semibold">${report.LDL}</p>
                    </div>
                </div>
            </div>
            
            ${prediction ? `
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-lg border-2 border-blue-200">
                    <h4 class="font-semibold text-xl mb-4 text-blue-900">AI Predictions</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-white rounded shadow">
                            <p class="text-sm text-gray-600">Ferritin</p>
                            <p class="text-2xl font-bold ${prediction.ferritin_abnormal ? 'text-red-600' : 'text-green-600'}">${parseFloat(prediction.Ferritin).toFixed(2)}</p>
                            <p class="text-xs mt-1 ${prediction.ferritin_abnormal ? 'text-red-500' : 'text-green-500'}">${prediction.ferritin_abnormal ? 'Abnormal' : 'Normal'}</p>
                        </div>
                        <div class="p-4 bg-white rounded shadow">
                            <p class="text-sm text-gray-600">Vitamin B12</p>
                            <p class="text-2xl font-bold ${prediction.b12_abnormal ? 'text-red-600' : 'text-green-600'}">${parseFloat(prediction.Vitamin_B12).toFixed(2)}</p>
                            <p class="text-xs mt-1 ${prediction.b12_abnormal ? 'text-red-500' : 'text-green-500'}">${prediction.b12_abnormal ? 'Abnormal' : 'Normal'}</p>
                        </div>
                        <div class="p-4 bg-white rounded shadow">
                            <p class="text-sm text-gray-600">CRP</p>
                            <p class="text-2xl font-bold ${prediction.crp_abnormal ? 'text-red-600' : 'text-green-600'}">${parseFloat(prediction.CRP).toFixed(2)}</p>
                            <p class="text-xs mt-1 ${prediction.crp_abnormal ? 'text-red-500' : 'text-green-500'}">${prediction.crp_abnormal ? 'Abnormal' : 'Normal'}</p>
                        </div>
                        <div class="p-4 bg-white rounded shadow">
                            <p class="text-sm text-gray-600">AFP</p>
                            <p class="text-2xl font-bold text-blue-600">${parseFloat(prediction.Afp).toFixed(2)}</p>
                        </div>
                        <div class="p-4 bg-white rounded shadow">
                            <p class="text-sm text-gray-600">HbA1c</p>
                            <p class="text-2xl font-bold ${prediction.hba1c_abnormal ? 'text-red-600' : 'text-green-600'}">${parseFloat(prediction.HbA1c).toFixed(2)}</p>
                            <p class="text-xs mt-1 ${prediction.hba1c_abnormal ? 'text-red-500' : 'text-green-500'}">${prediction.hba1c_abnormal ? 'Abnormal' : 'Normal'}</p>
                        </div>
                        <div class="p-4 bg-white rounded shadow">
                            <p class="text-sm text-gray-600">Cystatin C</p>
                            <p class="text-2xl font-bold ${prediction.cystatin_c_abnormal ? 'text-red-600' : 'text-green-600'}">${parseFloat(prediction.Cystatin_C).toFixed(2)}</p>
                            <p class="text-xs mt-1 ${prediction.cystatin_c_abnormal ? 'text-red-500' : 'text-green-500'}">${prediction.cystatin_c_abnormal ? 'Abnormal' : 'Normal'}</p>
                        </div>
                    </div>
                </div>
            ` : ''}
        </div>
    `;
    
    document.getElementById('reportModal').classList.remove('hidden');
}

function closeReportModal() {
    document.getElementById('reportModal').classList.add('hidden');
}

async function loadPredictions() {
    try {
        const response = await fetch(`${API_BASE}/api/patient/predictions`, {
            credentials: 'include'
        });
        
        const data = await response.json();
        
        if (data.success) {
            displayPredictions(data.predictions);
        }
    } catch (error) {
        console.error('Failed to load predictions:', error);
    }
}

function displayPredictions(predictions) {
    const container = document.getElementById('predictionsContainer');
    
    if (predictions.length === 0) {
        container.innerHTML = '<p class="text-center text-gray-500">No prediction history available</p>';
        return;
    }
    
    container.innerHTML = `
        <div class="space-y-4">
            ${predictions.map(pred => `
                <div class="border rounded-lg p-4 hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="font-semibold">Prediction #${pred.Prediction_ID}</h3>
                        <span class="text-sm text-gray-500">${new Date(pred.report_date).toLocaleDateString()}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-3 text-sm">
                        <div class="p-2 bg-gray-50 rounded">
                            <p class="text-gray-600">Ferritin</p>
                            <p class="font-semibold ${pred.ferritin_abnormal ? 'text-red-600' : 'text-green-600'}">${parseFloat(pred.Ferritin).toFixed(2)}</p>
                        </div>
                        <div class="p-2 bg-gray-50 rounded">
                            <p class="text-gray-600">Vitamin B12</p>
                            <p class="font-semibold ${pred.b12_abnormal ? 'text-red-600' : 'text-green-600'}">${parseFloat(pred.Vitamin_B12).toFixed(2)}</p>
                        </div>
                        <div class="p-2 bg-gray-50 rounded">
                            <p class="text-gray-600">HbA1c</p>
                            <p class="font-semibold ${pred.hba1c_abnormal ? 'text-red-600' : 'text-green-600'}">${parseFloat(pred.HbA1c).toFixed(2)}</p>
                        </div>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
}
