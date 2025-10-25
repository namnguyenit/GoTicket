import React from 'react';

interface Props {
  children: React.ReactNode;
}

interface State {
  hasError: boolean;
  error?: Error | null;
}

class ErrorBoundary extends React.Component<Props, State> {
  state: State = { hasError: false, error: null };

  static getDerivedStateFromError(error: Error): State {
    return { hasError: true, error };
  }

  componentDidCatch(error: Error, errorInfo: any) {
    // Ghi log để tiện chẩn đoán
    console.error('ErrorBoundary caught an error', error, errorInfo);
  }

  handleReset = () => {
    this.setState({ hasError: false, error: null });
    // Tải lại trang để khởi tạo lại trạng thái sạch
    if (typeof window !== 'undefined') {
      window.location.reload();
    }
  };

  render() {
    if (this.state.hasError) {
      return (
        <div className="flex min-h-screen flex-col items-center justify-center bg-gray-50 p-6 text-center">
          <div className="mb-4 text-2xl font-bold text-gray-800">Đã xảy ra lỗi khi hiển thị trang</div>
          <p className="mb-4 max-w-xl text-gray-600">
            Ứng dụng gặp lỗi không mong muốn. Bạn có thể tải lại trang. Nếu lỗi vẫn tiếp diễn, vui lòng kiểm tra Console hoặc liên hệ dev.
          </p>
          {this.state.error?.message && (
            <pre className="mb-6 max-h-40 w-full max-w-2xl overflow-auto rounded-md bg-red-50 p-3 text-left text-sm text-red-700">
              {this.state.error.message}
            </pre>
          )}
          <button
            onClick={this.handleReset}
            className="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white shadow hover:bg-blue-700"
          >
            Tải lại trang
          </button>
        </div>
      );
    }

    return this.props.children;
  }
}

export default ErrorBoundary;
