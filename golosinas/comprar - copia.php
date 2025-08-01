<?php
// Iniciar la sesión
require_once '../config/conexion.php';

// Verificar si el usuario está autenticado
$es_autenticado = isset($_SESSION['usuario_id']);
$descuento = 0;
if ($es_autenticado) {
    $usuario_id = $_SESSION['usuario_id'];
    $query = "SELECT membresia FROM usuarios WHERE id = '$usuario_id'";
    $resultado = mysqli_query($conexion, $query);
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $usuario = mysqli_fetch_assoc($resultado);
        $descuento = $usuario['membresia'] === 'premium' ? 0.20 : ($usuario['membresia'] === 'basica' ? 0.10 : 0);
    }
}

// Obtener golosinas disponibles
$query = "SELECT id, nombre, precio, stock FROM golosinas WHERE stock > 0";
$golosinas = mysqli_query($conexion, $query);
if (!$golosinas) {
    $error = 'Error al cargar golosinas: ' . mysqli_error($conexion);
} elseif (mysqli_num_rows($golosinas) == 0) {
    $error = 'No hay golosinas disponibles en este momento.';
}

// Obtener promoción
$promocion_id = 1; // Combo Familiar
$descuento_promocion = 0.15;
$query = "SELECT id, descuento FROM promociones WHERE id = 1 AND fecha_inicio <= NOW() AND fecha_fin >= NOW()";
$promocion = mysqli_query($conexion, $query);
if ($promocion && mysqli_num_rows($promocion) > 0) {
    $promo = mysqli_fetch_assoc($promocion);
    $promocion_id = $promo['id'];
    $descuento_promocion = $promo['descuento'];
}

// Incluir el encabezado
require_once '../includes/header.php';
?>

<!-- Contenido principal -->
<div class="max-w-2xl mx-auto py-8">
    <section class="animate-fade-in">
        <h2 class="text-3xl font-bold mb-6 text-center text-gray-800 animate-pulse">Comprar Golosinas</h2>
        <?php if (isset($error)) { ?>
            <p class="text-red-500 mb-4"><?php echo $error; ?></p>
        <?php } ?>
        <form id="golosina-form" method="POST" action="">
            <label class="block text-gray-700 mb-2">Selecciona una golosina:</label>
            <select name="golosina_id" id="golosina_id" class="w-full p-3 border rounded-md" required>
                <option value="">-- Selecciona --</option>
                <?php while ($golosina = mysqli_fetch_assoc($golosinas)) { ?>
                    <option value="<?php echo $golosina['id']; ?>" data-precio="<?php echo $golosina['precio']; ?>">
                        <?php echo htmlspecialchars($golosina['nombre'] . ' ($' . number_format($golosina['precio'], 2) . ', Stock: ' . $golosina['stock'] . ')'); ?>
                    </option>
                <?php } ?>
            </select>
            <label class="block text-gray-700 mb-2 mt-4">Cantidad:</label>
            <input type="number" name="cantidad" id="cantidad" class="w-full p-3 border rounded-md" min="1" max="10" required>
            <p class="text-gray-600 mt-4">
                Precio por golosina: <span id="precio-golosina">0.00</span>
                <?php if ($es_autenticado) { ?>
                    <br>Descuento membresía: <?php echo ($descuento * 100); ?>% (<?php echo $usuario['membresia']; ?>)
                <?php } ?>
                <br>Promoción: Combo Familiar (<?php echo ($descuento_promocion * 100); ?>% descuento)
                <br>Precio final: <span id="precio-final-golosina">0.00</span>
            </p>
            <button type="submit" class="btn btn-primary mt-6">Confirmar Compra</button>
        </form>
    </section>
</div>

<!-- JavaScript para manejar la compra -->
<script>
document.getElementById('golosina_id').addEventListener('change', function() {
    const precio = parseFloat(this.options[this.selectedIndex].getAttribute('data-precio') || 0);
    const descuento = <?php echo $descuento; ?>;
    const descuentoPromocion = <?php echo $descuento_promocion; ?>;
    document.getElementById('precio-golosina').textContent = '$' + precio.toFixed(2);
    document.getElementById('precio-final-golosina').textContent = '$' + (precio * (1 - descuento) * (1 - descuentoPromocion)).toFixed(2);
});

document.getElementById('golosina-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = {
        golosina_id: formData.get('golosina_id'),
        cantidad: parseInt(formData.get('cantidad')),
        promocion_id: <?php echo $promocion_id; ?>
    };

    // Validación
    if (!data.golosina_id || !data.cantidad || data.cantidad < 1 || data.cantidad > 10) {
        showToast('Por favor, selecciona una golosina y una cantidad válida.', true);
        return;
    }

    fetch('/cine/api/compra_golosina.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la red: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showToast('Compra de golosina realizada con éxito.', false);
            setTimeout(() => window.location.href = '/cine/compras/historial.php', 1000);
        } else {
            showToast('Error: ' + data.error, true);
        }
    })
    .catch(error => showToast('Error: ' + error.message, true));
});

// Función para mostrar notificaciones
function showToast(message, isError = false) {
    const toast = document.createElement('div');
    toast.className = `toast ${isError ? 'error' : 'success'}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 100);
    setTimeout(() => toast.remove(), 3000);
}
</script>

<!-- Estilos -->
<style>
/* Animación fade-in */
.animate-fade-in {
    animation: fadeIn 1s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Animación pulse */
.animate-pulse {
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Estilos para botones */
.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    font-weight: 500;
    text-align: center;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.btn:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
.btn-primary {
    background-color: #3b82f6;
    color: white;
}
.btn-primary:hover {
    background-color: #2563eb;
}

/* Estilos para formularios */
.text-red-500 { color: #ef4444; }
.text-gray-600 { color: #4b5563; }
.text-gray-700 { color: #4b5563; }
.text-3xl { font-size: 1.875rem; }
.font-bold { font-weight: 700; }
.mb-2 { margin-bottom: 0.5rem; }
.mb-4 { margin-bottom: 1rem; }
.mt-4 { margin-top: 1rem; }
.mt-6 { margin-top: 1.5rem; }
.w-full { width: 100%; }
.p-3 { padding: 0.75rem; }
.border { border: 1px solid #d1d5db; }
.rounded-md { border-radius: 6px; }
.text-center { text-align: center; }
.toast {
    position: fixed;
    bottom: 20px;
    right: 20px;
    padding: 1rem;
    border-radius: 6px;
    color: white;
    opacity: 0;
    transition: opacity 0.3s ease;
}
.toast.success { background: #10b981; }
.toast.error { background: #ef4444; }
.toast.show { opacity: 1; }
</style>

<?php
// Incluir el pie de página
require_once '../includes/footer.php';

// Cerrar la conexión
mysqli_close($conexion);
?>