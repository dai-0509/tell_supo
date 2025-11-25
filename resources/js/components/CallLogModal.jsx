import React, { useState, useEffect } from 'react';

const CallLogModal = () => {
  const [isOpen, setIsOpen] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState({});
  const [customers, setCustomers] = useState([]);
  const [formData, setFormData] = useState({
    customer_id: '',
    started_at: '',
    ended_at: '',
    result: '',
    notes: ''
  });

  useEffect(() => {
    console.log('Setting up call log modal event listeners');
    
    const handleButtonClick = () => {
      console.log('Call log modal button clicked');
      setIsOpen(true);
      // 現在時刻を開始時刻に設定
      const now = new Date();
      now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
      setFormData(prev => ({
        ...prev,
        started_at: now.toISOString().slice(0, 16)
      }));
    };
    
    const buttons = ['create-call-log-btn', 'create-call-log-btn-2'];
    const buttonElements = [];
    
    buttons.forEach(buttonId => {
      const button = document.getElementById(buttonId);
      if (button) {
        console.log(`Adding event listener to ${buttonId}`);
        button.addEventListener('click', handleButtonClick);
        buttonElements.push(button);
      }
    });
    
    return () => {
      buttonElements.forEach(button => {
        button.removeEventListener('click', handleButtonClick);
      });
    };
  }, []);

  // 顧客データを取得
  useEffect(() => {
    if (isOpen && customers.length === 0) {
      fetchCustomers();
    }
  }, [isOpen]);

  const fetchCustomers = async () => {
    try {
      const response = await fetch('/api/customers');
      if (response.ok) {
        const data = await response.json();
        setCustomers(data);
      }
    } catch (error) {
      console.error('Error fetching customers:', error);
    }
  };

  const closeModal = () => {
    setIsOpen(false);
    setErrors({});
    setFormData({
      customer_id: '',
      started_at: '',
      ended_at: '',
      result: '',
      notes: ''
    });
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
    
    if (errors[name]) {
      setErrors(prev => ({
        ...prev,
        [name]: []
      }));
    }
  };

  const handleSubmit = async () => {
    setIsLoading(true);
    setErrors({});

    try {
      const response = await fetch('/call-logs', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(formData)
      });

      const data = await response.json();

      if (response.ok && data.success) {
        closeModal();
        window.location.reload();
      } else {
        if (data.errors) {
          setErrors(data.errors);
        } else {
          alert(data.message || '架電記録の登録に失敗しました');
        }
      }
    } catch (error) {
      console.error('Error:', error);
      alert('ネットワークエラーが発生しました');
    } finally {
      setIsLoading(false);
    }
  };

  const handleBackdropClick = (e) => {
    if (e.target === e.currentTarget) {
      closeModal();
    }
  };

  const handleKeyDown = (e) => {
    if (e.key === 'Escape') {
      closeModal();
    }
  };

  if (!isOpen) return null;

  return (
    <div 
      className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      onClick={handleBackdropClick}
      onKeyDown={handleKeyDown}
      tabIndex={-1}
    >
      <div className="bg-white rounded-lg shadow-xl max-w-xl mx-auto my-6" style={{width: '90%', maxHeight: '85vh', overflowY: 'auto'}}>
        <div className="p-6">
          <div className="flex items-center justify-between mb-6">
            <h2 className="text-xl font-bold text-gray-800">新規架電記録</h2>
            <button
              onClick={closeModal}
              className="text-gray-400 hover:text-gray-600 text-2xl"
            >
              ×
            </button>
          </div>

          <form className="space-y-4">
            <div>
              <label className="form-label">顧客 *</label>
              <select
                name="customer_id"
                value={formData.customer_id}
                onChange={handleInputChange}
                className={`form-input ${errors.customer_id ? 'border-red-500' : ''}`}
              >
                <option value="">顧客を選択してください</option>
                {customers.map(customer => (
                  <option key={customer.id} value={customer.id}>
                    {customer.company_name} - {customer.contact_name}
                  </option>
                ))}
              </select>
              {errors.customer_id && (
                <div className="text-red-500 text-sm mt-1">
                  {errors.customer_id.join(', ')}
                </div>
              )}
            </div>

            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="form-label">開始時刻 *</label>
                <input
                  type="datetime-local"
                  name="started_at"
                  value={formData.started_at}
                  onChange={handleInputChange}
                  className={`form-input ${errors.started_at ? 'border-red-500' : ''}`}
                />
                {errors.started_at && (
                  <div className="text-red-500 text-sm mt-1">
                    {errors.started_at.join(', ')}
                  </div>
                )}
              </div>

              <div>
                <label className="form-label">終了時刻</label>
                <input
                  type="datetime-local"
                  name="ended_at"
                  value={formData.ended_at}
                  onChange={handleInputChange}
                  className={`form-input ${errors.ended_at ? 'border-red-500' : ''}`}
                />
                {errors.ended_at && (
                  <div className="text-red-500 text-sm mt-1">
                    {errors.ended_at.join(', ')}
                  </div>
                )}
              </div>
            </div>

            <div>
              <label className="form-label">結果 *</label>
              <select
                name="result"
                value={formData.result}
                onChange={handleInputChange}
                className={`form-input ${errors.result ? 'border-red-500' : ''}`}
              >
                <option value="">結果を選択してください</option>
                <option value="connected">通話成功</option>
                <option value="no_answer">不在</option>
                <option value="busy">話中</option>
                <option value="voicemail">留守電</option>
                <option value="wrong_number">番号違い</option>
                <option value="declined">断られた</option>
                <option value="callback_requested">折り返し希望</option>
                <option value="appointment_set">アポ獲得</option>
                <option value="not_interested">興味なし</option>
                <option value="follow_up_needed">フォローアップ必要</option>
              </select>
              {errors.result && (
                <div className="text-red-500 text-sm mt-1">
                  {errors.result.join(', ')}
                </div>
              )}
            </div>

            <div>
              <label className="form-label">メモ</label>
              <textarea
                name="notes"
                value={formData.notes}
                onChange={handleInputChange}
                rows={4}
                className="form-input"
                placeholder="通話内容や次回アクションについてのメモを入力..."
              />
              {errors.notes && (
                <div className="text-red-500 text-sm mt-1">
                  {errors.notes.join(', ')}
                </div>
              )}
            </div>
          </form>

          <div className="flex justify-end gap-3 mt-6 pt-4 border-t">
            <button
              onClick={closeModal}
              disabled={isLoading}
              className="btn-secondary"
            >
              キャンセル
            </button>
            <button
              onClick={handleSubmit}
              disabled={isLoading}
              className="btn-primary"
            >
              {isLoading ? (
                <>
                  <i className="fas fa-spinner fa-spin mr-2"></i>
                  作成中...
                </>
              ) : (
                '作成'
              )}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default CallLogModal;