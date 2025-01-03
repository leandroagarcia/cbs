// Aguarda o carregamento completo do DOM
document.addEventListener('DOMContentLoaded', function () {
    // Seleciona a div onde o conteúdo será exibido
    const conteudoDiv = document.getElementById('conteudo');

    // Caminho para o arquivo externo
    const arquivoTexto = 'conteudo/idosos.html'; // Altere se necessário

    // Realiza o fetch para carregar o arquivo
    fetch(arquivoTexto)
        .then(response => {
            // Verifica se o arquivo foi carregado com sucesso
            if (!response.ok) {
                throw new Error('Erro ao carregar o arquivo: ' + response.statusText);
            }
            return response.text();
        })
        .then(data => {
            // Insere o conteúdo do arquivo na div
            conteudoDiv.innerHTML = data;
        })
        .catch(error => {
            // Exibe uma mensagem de erro em caso de falha
            console.error('Erro:', error);
            conteudoDiv.innerHTML = '<p>Erro ao carregar o conteúdo. Tente novamente mais tarde.</p>';
        });
});
