class KpiWizard {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 4;
        this.data = {
            monthlyAppointmentTarget: null,
            calculation: null,
            weeklyDistribution: null,
            historicalData: null
        };
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.updateStepIndicator();
    }
    
    bindEvents() {
        document.addEventListener('DOMContentLoaded', () => {
            // Step navigation
            document.querySelectorAll('[data-next-step]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const nextStep = parseInt(btn.dataset.nextStep);
                    this.goToStep(nextStep);
                });
            });
            
            document.querySelectorAll('[data-prev-step]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const prevStep = parseInt(btn.dataset.prevStep);
                    this.goToStep(prevStep);
                });
            });
            
            // Calculation button
            const calculateBtn = document.getElementById('calculate-calls');
            if (calculateBtn) {
                calculateBtn.addEventListener('click', () => this.calculateCalls());
            }
            
            // Distribution buttons
            const autoDistributeBtn = document.getElementById('auto-distribute');
            const aiDistributeBtn = document.getElementById('ai-distribute');
            
            if (autoDistributeBtn) {
                autoDistributeBtn.addEventListener('click', () => this.distributeWeekly('auto'));
            }
            
            if (aiDistributeBtn) {
                aiDistributeBtn.addEventListener('click', () => this.distributeWeekly('ai_suggested'));
            }
            
            // Input change events for real-time updates
            const appointmentInput = document.getElementById('monthly_appointment_target');
            if (appointmentInput) {
                appointmentInput.addEventListener('input', () => {
                    this.data.monthlyAppointmentTarget = parseInt(appointmentInput.value);
                    this.updateCalculateButton();
                });
            }
            
            // Weekday input events
            this.bindWeekdayInputs();
        });
    }
    
    bindWeekdayInputs() {
        const weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        
        weekdays.forEach(day => {
            const input = document.getElementById(`${day}_call_target`);
            if (input) {
                input.addEventListener('input', () => this.updateWeeklyTotal());
            }
        });
    }
    
    goToStep(stepNumber) {
        if (stepNumber < 1 || stepNumber > this.totalSteps) return;
        
        // Validation before proceeding
        if (!this.validateCurrentStep()) {
            return;
        }
        
        // Hide current step
        document.querySelector(`#step-${this.currentStep}`).classList.add('hidden');
        
        // Show new step
        this.currentStep = stepNumber;
        document.querySelector(`#step-${this.currentStep}`).classList.remove('hidden');
        
        // Update indicator
        this.updateStepIndicator();
        
        // Special actions for certain steps
        if (this.currentStep === 4) {
            this.generateSummary();
        }
    }
    
    validateCurrentStep() {
        switch (this.currentStep) {
            case 1:
                const appointmentTarget = document.getElementById('monthly_appointment_target').value;
                if (!appointmentTarget || appointmentTarget <= 0) {
                    alert('月次アポ獲得目標を入力してください。');
                    return false;
                }
                return true;
                
            case 2:
                if (!this.data.calculation) {
                    alert('計算を実行してから次に進んでください。');
                    return false;
                }
                return true;
                
            case 3:
                if (!this.data.weeklyDistribution) {
                    alert('曜日別配分を設定してから次に進んでください。');
                    return false;
                }
                return true;
                
            default:
                return true;
        }
    }
    
    updateStepIndicator() {
        for (let i = 1; i <= this.totalSteps; i++) {
            const indicator = document.querySelector(`[data-step="${i}"]`);
            if (indicator) {
                if (i < this.currentStep) {
                    indicator.className = 'w-8 h-8 flex items-center justify-center rounded-full bg-green-500 text-white text-sm font-medium';
                } else if (i === this.currentStep) {
                    indicator.className = 'w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white text-sm font-medium';
                } else {
                    indicator.className = 'w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 text-gray-500 text-sm font-medium';
                }
            }
        }
    }
    
    updateCalculateButton() {
        const btn = document.getElementById('calculate-calls');
        const appointmentTarget = this.data.monthlyAppointmentTarget;
        
        if (btn) {
            if (appointmentTarget && appointmentTarget > 0) {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }
    }
    
    async calculateCalls() {
        const appointmentTarget = document.getElementById('monthly_appointment_target').value;
        
        if (!appointmentTarget || appointmentTarget <= 0) {
            alert('有効な月次アポ獲得目標を入力してください。');
            return;
        }
        
        try {
            this.showLoading('calculation-loading');
            
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const calculateUrl = document.querySelector('meta[name="calculate-calls-url"]').getAttribute('content');
            const response = await fetch(calculateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    monthly_appointment_target: parseInt(appointmentTarget)
                })
            });
            
            if (!response.ok) {
                throw new Error('計算に失敗しました');
            }
            
            const data = await response.json();
            this.data.calculation = data.calculation;
            this.data.historicalData = data.historical_data;
            
            this.displayCalculationResults(data);
            
        } catch (error) {
            console.error('Calculation error:', error);
            alert('計算エラーが発生しました: ' + error.message);
        } finally {
            this.hideLoading('calculation-loading');
        }
    }
    
    displayCalculationResults(data) {
        const resultsContainer = document.getElementById('calculation-results');
        const { calculation, historical_data } = data;
        
        resultsContainer.innerHTML = `
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-4">📊 計算結果</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">${calculation.required_total_calls.toLocaleString()}</div>
                        <div class="text-sm text-gray-600">月次推奨架電数</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">${calculation.weekly_target.toLocaleString()}</div>
                        <div class="text-sm text-gray-600">週次目標架電数</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">${Math.round(calculation.required_successful_calls).toLocaleString()}</div>
                        <div class="text-sm text-gray-600">必要成功通話数</div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white border rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-3">使用した成功率</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">通話成功率:</span>
                            <span class="font-medium">${calculation.success_rate_used}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">アポ獲得率:</span>
                            <span class="font-medium">${calculation.appointment_rate_used}%</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white border rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-3">過去実績 (3ヶ月)</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">平均通話成功率:</span>
                            <span class="font-medium">${historical_data.success_rate || 'データなし'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">平均アポ獲得率:</span>
                            <span class="font-medium">${historical_data.appointment_rate || 'データなし'}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-center space-x-4">
                <input type="hidden" id="calculated_monthly_target" value="${calculation.required_total_calls}">
                <input type="hidden" id="calculated_weekly_target" value="${calculation.weekly_target}">
                <button type="button" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200"
                        data-next-step="3">
                    この計算で続行 →
                </button>
                <button type="button" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition duration-200"
                        onclick="document.getElementById('manual-adjustment').classList.remove('hidden')">
                    手動調整
                </button>
            </div>
            
            <div id="manual-adjustment" class="hidden mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <h4 class="font-medium text-gray-900 mb-3">手動調整</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">月次目標架電数</label>
                        <input type="number" id="manual_monthly_target" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="${calculation.required_total_calls}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">週次目標架電数</label>
                        <input type="number" id="manual_weekly_target" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="${calculation.weekly_target}">
                    </div>
                </div>
                <div class="mt-4 flex justify-center">
                    <button type="button" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700"
                            onclick="this.useManualValues()">
                        手動設定値を使用
                    </button>
                </div>
            </div>
        `;
        
        // Re-bind event listeners for new buttons
        resultsContainer.querySelectorAll('[data-next-step]').forEach(btn => {
            btn.addEventListener('click', () => {
                const nextStep = parseInt(btn.dataset.nextStep);
                this.goToStep(nextStep);
            });
        });
        
        resultsContainer.classList.remove('hidden');
    }
    
    useManualValues() {
        const manualMonthly = document.getElementById('manual_monthly_target').value;
        const manualWeekly = document.getElementById('manual_weekly_target').value;
        
        if (manualMonthly && manualWeekly) {
            document.getElementById('calculated_monthly_target').value = manualMonthly;
            document.getElementById('calculated_weekly_target').value = manualWeekly;
            
            this.data.calculation.required_total_calls = parseInt(manualMonthly);
            this.data.calculation.weekly_target = parseInt(manualWeekly);
            
            alert('手動設定値を適用しました。');
            document.getElementById('manual-adjustment').classList.add('hidden');
        }
    }
    
    async distributeWeekly(method) {
        const weeklyTarget = document.getElementById('calculated_weekly_target').value;
        
        if (!weeklyTarget) {
            alert('週次目標が設定されていません。');
            return;
        }
        
        try {
            this.showLoading('distribution-loading');
            
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const distributeUrl = document.querySelector('meta[name="distribute-weekly-url"]').getAttribute('content');
            const response = await fetch(distributeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    weekly_target: parseInt(weeklyTarget),
                    method: method
                })
            });
            
            if (!response.ok) {
                throw new Error('配分計算に失敗しました');
            }
            
            const data = await response.json();
            this.data.weeklyDistribution = data.distribution;
            
            this.applyWeekdayDistribution(data.distribution, method);
            
        } catch (error) {
            console.error('Distribution error:', error);
            alert('配分エラーが発生しました: ' + error.message);
        } finally {
            this.hideLoading('distribution-loading');
        }
    }
    
    applyWeekdayDistribution(distribution, method) {
        const weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        
        weekdays.forEach(day => {
            const input = document.getElementById(`${day}_call_target`);
            if (input && distribution[day] !== undefined) {
                input.value = distribution[day];
            }
        });
        
        this.updateWeeklyTotal();
        
        // Show method used
        const methodMessage = method === 'ai_suggested' 
            ? '🤖 AI推奨配分を適用しました（過去実績に基づく最適化）'
            : '⚖️ 自動配分を適用しました（均等配分）';
            
        this.showMessage(methodMessage, 'success');
    }
    
    updateWeeklyTotal() {
        const weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        let total = 0;
        
        weekdays.forEach(day => {
            const input = document.getElementById(`${day}_call_target`);
            if (input && input.value) {
                total += parseInt(input.value) || 0;
            }
        });
        
        const totalDisplay = document.getElementById('weekday-total');
        const targetDisplay = document.getElementById('weekly-target-display');
        const weeklyTarget = parseInt(document.getElementById('calculated_weekly_target').value);
        
        if (totalDisplay) {
            totalDisplay.textContent = total.toLocaleString();
            totalDisplay.className = total === weeklyTarget ? 'text-green-600 font-bold' : 'text-red-600 font-bold';
        }
        
        if (targetDisplay) {
            targetDisplay.textContent = weeklyTarget.toLocaleString();
        }
        
        // Update hidden inputs for form submission
        document.getElementById('weekly_call_target').value = weeklyTarget;
        document.getElementById('monthly_call_target').value = document.getElementById('calculated_monthly_target').value;
    }
    
    generateSummary() {
        const summaryContainer = document.getElementById('summary-content');
        const appointmentTarget = document.getElementById('monthly_appointment_target').value;
        const monthlyTarget = document.getElementById('calculated_monthly_target').value;
        const weeklyTarget = document.getElementById('calculated_weekly_target').value;
        
        const weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        const weekdayLabels = ['月曜', '火曜', '水曜', '木曜', '金曜', '土曜', '日曜'];
        
        let weekdayTargets = '';
        weekdays.forEach((day, index) => {
            const input = document.getElementById(`${day}_call_target`);
            const value = input ? input.value || 0 : 0;
            if (value > 0) {
                weekdayTargets += `
                    <div class="flex justify-between">
                        <span>${weekdayLabels[index]}:</span>
                        <span class="font-medium">${parseInt(value).toLocaleString()}件</span>
                    </div>
                `;
            }
        });
        
        summaryContainer.innerHTML = `
            <div class="space-y-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4">📈 目標設定サマリー</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-blue-600">${parseInt(appointmentTarget).toLocaleString()}</div>
                            <div class="text-sm text-gray-600">月次アポ獲得目標</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-green-600">${parseInt(monthlyTarget).toLocaleString()}</div>
                            <div class="text-sm text-gray-600">月次架電目標</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-purple-600">${parseInt(weeklyTarget).toLocaleString()}</div>
                            <div class="text-sm text-gray-600">週次架電目標</div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white border rounded-lg p-6">
                    <h4 class="font-medium text-gray-900 mb-4">📅 曜日別目標配分</h4>
                    <div class="space-y-2">
                        ${weekdayTargets}
                    </div>
                </div>
            </div>
        `;
    }
    
    showLoading(elementId) {
        const element = document.getElementById(elementId);
        if (element) {
            element.classList.remove('hidden');
        }
    }
    
    hideLoading(elementId) {
        const element = document.getElementById(elementId);
        if (element) {
            element.classList.add('hidden');
        }
    }
    
    showMessage(message, type = 'info') {
        const alertClass = type === 'success' ? 'bg-green-100 border-green-300 text-green-800' :
                          type === 'error' ? 'bg-red-100 border-red-300 text-red-800' :
                          'bg-blue-100 border-blue-300 text-blue-800';
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `fixed top-4 right-4 z-50 p-4 border rounded-lg ${alertClass} shadow-lg`;
        messageDiv.textContent = message;
        
        document.body.appendChild(messageDiv);
        
        setTimeout(() => {
            messageDiv.remove();
        }, 3000);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new KpiWizard();
});