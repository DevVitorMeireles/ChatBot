document
  .getElementById("user-input")
  .addEventListener("keypress", function (event) {
    if (event.key === "Enter") {
      sendMessage();
    }
  });

function sendMessage() {
  const input = document.getElementById("user-input");
  const text = input.value.trim();
  if (!text) return;

  appendMessage("Você", text, "user");
  input.value = "";

  const data = JSON.stringify({ mensagem: text });
  console.log("JSON sendo enviado:", data);

  fetch("http://localhost:8889/chatbot.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: data,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.text(); //  Pegar o texto primeiro
    })
    .then((text) => {
      console.log("Resposta do servidor (texto):", text);
      try {
        const jsonData = JSON.parse(text);
        console.log("Resposta do servidor (JSON):", jsonData);
        appendMessage("Bot", jsonData.resposta, "bot");
      } catch (error) {
        console.error("Erro ao processar JSON:", error);
        appendMessage("Bot", "Erro ao processar resposta do servidor.", "bot");
      }
    })
    .catch((error) => {
      console.error("Erro na requisição:", error);
      appendMessage("Bot", `Erro: ${error.message}`, "bot");
    });
}

function appendMessage(nome, texto, classe) {
  const box = document.getElementById("chat-box");
  const div = document.createElement("div");
  div.classList.add("message", classe);
  div.innerHTML = `<strong>${nome}:</strong> ${texto}`;
  box.appendChild(div);
  box.scrollTop = box.scrollHeight;
}
