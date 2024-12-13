<?php
                // Aquí se mostrarían las reseñas desde la base de datos
                // Supongamos que se tiene una variable $reseñas obtenida de la base de datos.
                foreach ($reseñas as $index => $reseña) {
                    $active = $index === 0 ? 'active' : '';
                    echo "
                    <div class='carousel-item $active'>
                        <p class='mb-1'><strong>{$reseña['nombre']}:</strong></p>
                        <p>{$reseña['mensaje']}</p>
                    </div>";
                }
                ?>