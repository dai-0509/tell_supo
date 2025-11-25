import './bootstrap';
import { createRoot } from 'react-dom/client';
import CustomerModal from './components/CustomerModal.jsx';
import CallLogModal from './components/CallLogModal.jsx';

// Reactコンポーネントの初期化
function initializeReactComponents() {
    console.log('Initializing React components...');
    
    // 顧客作成モーダル
    const customerModalContainer = document.getElementById('customer-modal-root');
    if (customerModalContainer) {
        console.log('Mounting CustomerModal');
        const root = createRoot(customerModalContainer);
        root.render(<CustomerModal />);
    } else {
        console.log('customer-modal-root not found');
    }

    // 架電記録作成モーダル  
    const callLogModalContainer = document.getElementById('call-log-modal-root');
    if (callLogModalContainer) {
        console.log('Mounting CallLogModal');
        const root = createRoot(callLogModalContainer);
        root.render(<CallLogModal />);
    } else {
        console.log('call-log-modal-root not found');
    }
}

// DOMContentLoadedで初期化
document.addEventListener('DOMContentLoaded', initializeReactComponents);

// HMR対応
if ((import.meta as any).hot) {
    (import.meta as any).hot.accept();
}