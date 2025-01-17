<?php
  require_once(dirname(__DIR__) . "../../conexao.php");

  if (isset($_POST['query'])) {
      $query = $_POST['query'];
      $selection_type = $_POST['selection_type'] ?? 'single'; // Default to single selection if not provided

      try {
          // Consulta pelo código ou descrição no banco de dados
          $sql = "SELECT codigo, descricao FROM lpco WHERE codigo LIKE :query OR descricao LIKE :query";
          $stmt = $pdo->prepare($sql);
          $likeQuery = "%" . $query . "%";
          $stmt->bindParam(':query', $likeQuery, PDO::PARAM_STR);
          $stmt->execute();
          $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

          $output = '';
          if ($stmt->rowCount() > 0) {
              foreach ($result as $row) {
                  if ($selection_type === 'multiple') {
                      $output .= '<div class="list-group-item">';
                      $output .= '<input type="checkbox" class="form-check-input modeloLpcoCheckbox" value="' . htmlspecialchars($row['codigo']) . ' - ' . htmlspecialchars($row['descricao']) . '"> ';
                      $output .= '<label class="form-check-label">' . htmlspecialchars($row['codigo']) . ' - ' . htmlspecialchars($row['descricao']) . '</label>';
                      $output .= '</div>';
                  } else {
                      $output .= '<a href="#" class="list-group-item list-group-item-action" data-codigo="' . htmlspecialchars($row['codigo']) . '">' . htmlspecialchars($row['codigo']) . ' - ' . htmlspecialchars($row['descricao']) . '</a>';
                  }
              }
          } else {
              $output .= '<div class="list-group-item list-group-item-action disabled">Nenhum resultado encontrado</div>';
          }

          echo $output;
      } catch (PDOException $e) {
          echo '<div class="list-group-item list-group-item-action disabled">Erro na consulta: ' . htmlspecialchars($e->getMessage()) . '</div>';
      }
  }
?>
