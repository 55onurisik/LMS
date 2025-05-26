@extends('layouts.admin')

@section('title', 'Yapay Zeka Analizi')

@section('page_title', 'Yapay Zeka Destekli Öğrenci Analizi')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Öğrenci Adı</th>
                                <th>E-posta</th>
                                <th>Son Analiz Tarihi</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>{{ $student->last_analysis_at ? $student->last_analysis_at->format('d.m.Y H:i') : 'Henüz analiz yapılmadı' }}</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" onclick="showAnalysis({{ $student->id }})">
                                            <i class="fa fa-chart-line"></i> Analiz
                                        </button>
                                        <button class="btn btn-info btn-sm" onclick="openChatbot({{ $student->id }})">
                                            <i class="fa fa-robot"></i> Yapay Zekaya Sor
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Analiz Modal -->
    <div class="modal fade" id="analysisModal" tabindex="-1" role="dialog" aria-labelledby="analysisModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="analysisModalLabel">Öğrenci Analizi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadingSpinner" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Yükleniyor...</span>
                        </div>
                        <p class="mt-2">Analiz yapılıyor, lütfen bekleyin...</p>
                    </div>
                    <div id="analysisContent" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chatbot Modal -->
    <div class="modal fade" id="chatbotModal" tabindex="-1" role="dialog" aria-labelledby="chatbotModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="chatbotModalLabel">Yapay Zeka Asistanı</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="chat-container" style="height: 400px; overflow-y: auto; margin-bottom: 20px;">
                        <div id="chatMessages"></div>
                    </div>
                    <div class="input-group">
                        <input type="text" id="chatInput" class="form-control" placeholder="Sorunuzu yazın...">
                        <div class="input-group-append">
                            <button class="btn btn-primary" onclick="sendMessage()">
                                <i class="fa fa-paper-plane"></i> Gönder
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
let currentStudentId = null;

function showAnalysis(studentId) {
    $('#analysisModal').modal('show');
    $('#loadingSpinner').show();
    $('#analysisContent').hide();
    
    fetch(`/api/students/${studentId}/analysis`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        $('#loadingSpinner').hide();
        $('#analysisContent').show();
        
        if (data.success) {
            $('#analysisContent').html(data.analysis);
        } else {
            $('#analysisContent').html('<div class="alert alert-danger">' + (data.message || 'Analiz yapılırken bir hata oluştu') + '</div>');
        }
    })
    .catch(error => {
        $('#loadingSpinner').hide();
        $('#analysisContent').show();
        $('#analysisContent').html('<div class="alert alert-danger">Analiz yapılırken bir hata oluştu: ' + error.message + '</div>');
    });
}

function openChatbot(studentId) {
    currentStudentId = studentId;
    $('#chatbotModal').modal('show');
    document.getElementById('chatMessages').innerHTML = '';
    addMessage('Merhaba! Ben öğrenci performans analiz asistanınız. Size nasıl yardımcı olabilirim?', 'assistant');
}

function addMessage(content, role) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${role}-message`;
    messageDiv.innerHTML = `
        <div class="message-content">
            <strong>${role === 'assistant' ? 'Asistan' : 'Siz'}:</strong>
            <p>${content}</p>
        </div>
    `;
    document.getElementById('chatMessages').appendChild(messageDiv);
    const chatContainer = document.querySelector('.chat-container');
    chatContainer.scrollTop = chatContainer.scrollHeight;
}

function sendMessage() {
    const messageInput = document.getElementById('chatInput');
    const message = messageInput.value.trim();
    
    if (!message) return;
    
    // Kullanıcı mesajını ekle
    addMessage(message, 'user');
    messageInput.value = '';
    
    // Typing göstergesini ekle
    const typingIndicator = document.createElement('div');
    typingIndicator.className = 'message assistant-message';
    typingIndicator.innerHTML = `
        <div class="message-content">
            <strong>Asistan:</strong>
            <div class="typing-indicator">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    `;
    document.getElementById('chatMessages').appendChild(typingIndicator);
    
    // API'ye istek gönder
    fetch(`/api/students/${currentStudentId}/chat`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ message })
    })
    .then(response => response.json())
    .then(data => {
        // Typing göstergesini kaldır
        typingIndicator.remove();
        
        if (data.success) {
            addMessage(data.response, 'assistant');
        } else {
            addMessage(data.message || 'Üzgünüm, bir hata oluştu. Lütfen tekrar deneyin.', 'assistant');
        }
    })
    .catch(error => {
        // Typing göstergesini kaldır
        typingIndicator.remove();
        addMessage('Bir hata oluştu. Lütfen tekrar deneyin.', 'assistant');
    });
}

// Enter tuşu ile mesaj gönderme
$('#chatInput').keypress(function(e) {
    if (e.which == 13) {
        sendMessage();
    }
});

// Modal kapatıldığında içeriği temizle
$('#analysisModal').on('hidden.bs.modal', function () {
    $('#analysisContent').hide();
    $('#loadingSpinner').show();
    $('#analysisContent').html('');
});
</script>

<style>
.message {
    margin: 10px 0;
    padding: 10px;
    border-radius: 10px;
}

.user-message {
    background-color: #e3f2fd;
    margin-left: 20%;
}

.assistant-message {
    background-color: #f5f5f5;
    margin-right: 20%;
}

.message-content {
    word-wrap: break-word;
}

.typing-indicator {
    display: flex;
    align-items: center;
    gap: 4px;
}

.typing-indicator span {
    width: 8px;
    height: 8px;
    background: #666;
    border-radius: 50%;
    animation: typing 1s infinite;
}

.typing-indicator span:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-indicator span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}
</style>
@endsection 