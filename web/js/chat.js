import { initializeApp } from 'https://www.gstatic.com/firebasejs/12.6.0/firebase-app.js';
import { getAuth, signInWithCustomToken } from 'https://www.gstatic.com/firebasejs/12.6.0/firebase-auth.js';
import { getDatabase, onValue, push, ref, set, update } from 'https://www.gstatic.com/firebasejs/12.6.0/firebase-database.js';

const configNode = document.getElementById('chat-config');

if (configNode) {
  const config = JSON.parse(configNode.textContent);
  const chat = document.querySelector('.chat');
  const form = chat.querySelector('.chat__form');
  const field = chat.querySelector('.chat__form-message');
  const conversation = chat.querySelector('.chat__conversation');
  const status = chat.querySelector('.chat__status');
  const dialogSelect = chat.querySelector('.chat__dialog-select');
  let database;
  let activeDialog = null;
  let unsubscribe = null;

  const setStatus = (text) => {
    status.textContent = text;
  };

  const fetchJson = async (url, options = {}) => {
    const response = await fetch(url, {
      credentials: 'same-origin',
      headers: { 'X-Requested-With': 'XMLHttpRequest', ...(options.headers || {}) },
      ...options,
    });
    if (!response.ok) {
      const body = await response.json().catch(() => ({}));
      throw new Error(body.message || `Ошибка сервера (${response.status})`);
    }
    return response.json();
  };

  const post = (url, data) => {
    const body = new URLSearchParams({ ...data, [config.csrfParam]: config.csrfToken });
    return fetchJson(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
      body,
    });
  };

  const participantName = (id) => {
    if (!activeDialog) return '';
    return id === activeDialog.sellerId ? activeDialog.sellerName : activeDialog.buyerName;
  };

  const markIncomingRead = (messages) => {
    Object.entries(messages).forEach(([id, message]) => {
      if (message.recipientId === config.currentUserId && message.read === false) {
        update(ref(database, `chats/${config.offerId}/${activeDialog.buyerId}/messages/${id}`), { read: true })
          .catch(() => setStatus('Не удалось отметить сообщение прочитанным'));
      }
    });
  };

  const renderMessages = (messages) => {
    conversation.innerHTML = '';
    Object.values(messages)
      .sort((a, b) => a.createdAt - b.createdAt)
      .forEach((message) => {
        const item = document.createElement('li');
        item.className = 'chat__message';

        const title = document.createElement('div');
        title.className = 'chat__message-title';
        const author = document.createElement('p');
        author.className = 'chat__message-author';
        author.textContent = participantName(message.senderId);
        const time = document.createElement('p');
        time.className = 'chat__message-time';
        time.textContent = new Date(message.createdAt).toLocaleString('ru-RU', {
          day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit',
        });
        title.append(author, time);

        const content = document.createElement('div');
        content.className = 'chat__message-content';
        const text = document.createElement('p');
        text.textContent = message.text;
        content.append(text);
        item.append(title, content);
        conversation.append(item);
      });
    conversation.scrollTop = conversation.scrollHeight;
    markIncomingRead(messages);
  };

  const selectDialog = (dialog) => {
    if (!dialog || activeDialog?.buyerId === dialog.buyerId) return;
    if (unsubscribe) unsubscribe();
    activeDialog = dialog;
    field.disabled = false;
    setStatus(`Диалог: ${config.isSeller ? dialog.buyerName : dialog.sellerName}`);
    unsubscribe = onValue(
      ref(database, `chats/${config.offerId}/${dialog.buyerId}/messages`),
      (snapshot) => renderMessages(snapshot.val() || {}),
      (error) => setStatus(`Ошибка доступа: ${error.message}`)
    );
  };

  const loadSellerDialogs = async () => {
    const { dialogs } = await fetchJson(config.dialogsUrl);
    const previous = dialogSelect.value;
    dialogSelect.innerHTML = '';
    const empty = document.createElement('option');
    empty.value = '';
    empty.textContent = dialogs.length ? 'Выберите покупателя' : 'Нет диалогов';
    dialogSelect.append(empty);
    dialogs.forEach((dialog) => {
      const option = document.createElement('option');
      option.value = dialog.buyerId;
      option.textContent = dialog.buyerName;
      option.dataset.dialog = JSON.stringify(dialog);
      dialogSelect.append(option);
    });
    if (previous && dialogs.some((dialog) => dialog.buyerId === previous)) {
      dialogSelect.value = previous;
    } else if (dialogs.length === 1) {
      dialogSelect.value = dialogs[0].buyerId;
      selectDialog(dialogs[0]);
    }
    if (!dialogs.length) {
      field.disabled = true;
      setStatus('Покупатели ещё не начинали диалог');
    }
  };

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const text = field.value.trim();
    if (!text || !activeDialog) return;
    const recipientId = config.currentUserId === activeDialog.sellerId
      ? activeDialog.buyerId
      : activeDialog.sellerId;
    try {
      await set(push(ref(database, `chats/${config.offerId}/${activeDialog.buyerId}/messages`)), {
        senderId: config.currentUserId,
        recipientId,
        text,
        createdAt: Date.now(),
        read: false,
        notified: false,
      });
      field.value = '';
    } catch (error) {
      setStatus(`Сообщение не отправлено: ${error.message}`);
    }
  });

  dialogSelect?.addEventListener('change', () => {
    const option = dialogSelect.selectedOptions[0];
    selectDialog(option?.dataset.dialog ? JSON.parse(option.dataset.dialog) : null);
  });

  (async () => {
    try {
      const app = initializeApp(config.firebase);
      database = getDatabase(app);
      const { token } = await fetchJson(config.tokenUrl);
      await signInWithCustomToken(getAuth(app), token);
      if (config.isSeller) {
        await loadSellerDialogs();
        window.setInterval(() => loadSellerDialogs().catch(() => {}), 5000);
      } else {
        selectDialog(await post(config.openUrl, { offerId: config.offerId }));
      }
    } catch (error) {
      field.disabled = true;
      setStatus(`Чат недоступен: ${error.message}`);
    }
  })();
}
