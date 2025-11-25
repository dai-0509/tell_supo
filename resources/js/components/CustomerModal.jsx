import React, { useState, useEffect } from 'react';

const CustomerModal = () => {
  const [isOpen, setIsOpen] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState({});
  const [formData, setFormData] = useState({
    company_name: '',
    contact_name: '',
    email: '',
    phone: '',
    industry: '',
    area: '',
    temperature_rating: '',
    priority: '3',
    status: 'new',
    memo: ''
  });

  useEffect(() => {
    console.log('Setting up customer modal event listeners');
    
    const handleButtonClick = () => {
      console.log('Customer modal button clicked');
      setIsOpen(true);
    };
    
    const buttons = ['create-customer-btn', 'create-customer-btn-2'];
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

  const closeModal = () => {
    setIsOpen(false);
    setErrors({});
    setFormData({
      company_name: '',
      contact_name: '',
      email: '',
      phone: '',
      industry: '',
      area: '',
      temperature_rating: '',
      priority: '3',
      status: 'new',
      memo: ''
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
      const response = await fetch('/customers', {
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
          alert(data.message || '顧客の登録に失敗しました');
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
            <h2 className="text-xl font-bold text-gray-800">新規顧客登録</h2>
            <button
              onClick={closeModal}
              className="text-gray-400 hover:text-gray-600 text-2xl"
            >
              ×
            </button>
          </div>

          <form className="space-y-4">
            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="form-label">会社名 *</label>
                <input
                  type="text"
                  name="company_name"
                  value={formData.company_name}
                  onChange={handleInputChange}
                  className={`form-input ${errors.company_name ? 'border-red-500' : ''}`}
                />
                {errors.company_name && (
                  <div className="text-red-500 text-sm mt-1">
                    {errors.company_name.join(', ')}
                  </div>
                )}
              </div>

              <div>
                <label className="form-label">担当者名 *</label>
                <input
                  type="text"
                  name="contact_name"
                  value={formData.contact_name}
                  onChange={handleInputChange}
                  className={`form-input ${errors.contact_name ? 'border-red-500' : ''}`}
                />
                {errors.contact_name && (
                  <div className="text-red-500 text-sm mt-1">
                    {errors.contact_name.join(', ')}
                  </div>
                )}
              </div>
            </div>

            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="form-label">メールアドレス</label>
                <input
                  type="email"
                  name="email"
                  value={formData.email}
                  onChange={handleInputChange}
                  className={`form-input ${errors.email ? 'border-red-500' : ''}`}
                />
                {errors.email && (
                  <div className="text-red-500 text-sm mt-1">
                    {errors.email.join(', ')}
                  </div>
                )}
              </div>

              <div>
                <label className="form-label">電話番号</label>
                <input
                  type="tel"
                  name="phone"
                  value={formData.phone}
                  onChange={handleInputChange}
                  className={`form-input ${errors.phone ? 'border-red-500' : ''}`}
                />
                {errors.phone && (
                  <div className="text-red-500 text-sm mt-1">
                    {errors.phone.join(', ')}
                  </div>
                )}
              </div>
            </div>

            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="form-label">業界</label>
                <select
                  name="industry"
                  value={formData.industry}
                  onChange={handleInputChange}
                  className="form-input"
                >
                  <option value="">選択してください</option>
                  <option value="IT・ソフトウェア">IT・ソフトウェア</option>
                  <option value="製造業">製造業</option>
                  <option value="商社・卸売">商社・卸売</option>
                  <option value="小売・サービス">小売・サービス</option>
                  <option value="金融・保険">金融・保険</option>
                  <option value="建設・不動産">建設・不動産</option>
                  <option value="医療・福祉">医療・福祉</option>
                  <option value="教育">教育</option>
                  <option value="その他">その他</option>
                </select>
              </div>

              <div>
                <label className="form-label">エリア</label>
                <select
                  name="area"
                  value={formData.area}
                  onChange={handleInputChange}
                  className="form-input"
                >
                  <option value="">選択してください</option>
                  <option value="北海道">北海道</option>
                  <option value="東北">東北</option>
                  <option value="関東">関東</option>
                  <option value="中部">中部</option>
                  <option value="近畿">近畿</option>
                  <option value="中国">中国</option>
                  <option value="四国">四国</option>
                  <option value="九州・沖縄">九州・沖縄</option>
                </select>
              </div>
            </div>

            <div className="grid grid-cols-3 gap-4">
              <div>
                <label className="form-label">温度感</label>
                <select
                  name="temperature_rating"
                  value={formData.temperature_rating}
                  onChange={handleInputChange}
                  className="form-input"
                >
                  <option value="">選択してください</option>
                  <option value="高">高</option>
                  <option value="中">中</option>
                  <option value="低">低</option>
                </select>
              </div>

              <div>
                <label className="form-label">優先度 *</label>
                <select
                  name="priority"
                  value={formData.priority}
                  onChange={handleInputChange}
                  className={`form-input ${errors.priority ? 'border-red-500' : ''}`}
                >
                  <option value="1">1 (最高)</option>
                  <option value="2">2 (高)</option>
                  <option value="3">3 (中)</option>
                  <option value="4">4 (低)</option>
                  <option value="5">5 (最低)</option>
                </select>
                {errors.priority && (
                  <div className="text-red-500 text-sm mt-1">
                    {errors.priority.join(', ')}
                  </div>
                )}
              </div>

              <div>
                <label className="form-label">ステータス *</label>
                <select
                  name="status"
                  value={formData.status}
                  onChange={handleInputChange}
                  className={`form-input ${errors.status ? 'border-red-500' : ''}`}
                >
                  <option value="new">新規</option>
                  <option value="contacted">連絡済み</option>
                  <option value="qualified">見込み客</option>
                  <option value="proposal">提案中</option>
                  <option value="negotiation">商談中</option>
                  <option value="closed_won">成約</option>
                  <option value="closed_lost">失注</option>
                </select>
                {errors.status && (
                  <div className="text-red-500 text-sm mt-1">
                    {errors.status.join(', ')}
                  </div>
                )}
              </div>
            </div>

            <div>
              <label className="form-label">メモ</label>
              <textarea
                name="memo"
                value={formData.memo}
                onChange={handleInputChange}
                rows={4}
                className="form-input"
                placeholder="顧客に関する追加情報やメモを入力..."
              />
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

export default CustomerModal;