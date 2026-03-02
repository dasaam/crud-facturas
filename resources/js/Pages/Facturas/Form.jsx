import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { useMemo } from 'react';

function formatearMoneda(cantidad) {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN',
    }).format(cantidad ?? 0);
}

function calcularTotales(items) {
    return items.reduce(
        (acumulado, item) => {
            const cantidad = Number(item.cantidad || 0);
            const precioUnitario = Number(item.precioUnitario || 0);
            const porcentajeImpuesto = Number(item.porcentajeImpuesto || 0);
            const porcentajeDescuento = Number(item.porcentajeDescuento || 0);

            const subtotalLinea = cantidad * precioUnitario;
            const descuentoLinea = subtotalLinea * (porcentajeDescuento / 100);
            const baseImponible = subtotalLinea - descuentoLinea;
            const impuestoLinea = baseImponible * (porcentajeImpuesto / 100);

            acumulado.subtotal += subtotalLinea;
            acumulado.descuento += descuentoLinea;
            acumulado.impuesto += impuestoLinea;
            acumulado.total += baseImponible + impuestoLinea;

            return acumulado;
        },
        { subtotal: 0, descuento: 0, impuesto: 0, total: 0 },
    );
}

function obtenerLineaCalculada(item) {
    const cantidad = Number(item.cantidad || 0);
    const precioUnitario = Number(item.precioUnitario || 0);
    const porcentajeImpuesto = Number(item.porcentajeImpuesto || 0);
    const porcentajeDescuento = Number(item.porcentajeDescuento || 0);
    const subtotalLinea = cantidad * precioUnitario;
    const descuentoLinea = subtotalLinea * (porcentajeDescuento / 100);
    const baseImponible = subtotalLinea - descuentoLinea;
    const impuestoLinea = baseImponible * (porcentajeImpuesto / 100);

    return baseImponible + impuestoLinea;
}

export default function Form({ modoEdicion, factura, clientes, productos }) {
    const facturaInicial = factura ?? {
        clienteId: '',
        fechaEmision: '',
        fechaVencimiento: '',
        moneda: 'MXN',
        notas: '',
        items: [
            {
                productoId: '',
                descripcion: '',
                cantidad: '1',
                precioUnitario: '0',
                porcentajeImpuesto: '16',
                porcentajeDescuento: '0',
            },
        ],
    };

    const { data, setData, post, put, processing, errors } = useForm({
        clienteId: facturaInicial.clienteId,
        fechaEmision: facturaInicial.fechaEmision,
        fechaVencimiento: facturaInicial.fechaVencimiento,
        moneda: facturaInicial.moneda,
        notas: facturaInicial.notas ?? '',
        items: facturaInicial.items,
    });

    const formularioBloqueado = modoEdicion && factura?.estado === 'cancelada';
    const totalesCalculados = useMemo(() => calcularTotales(data.items), [data.items]);

    const obtenerErrorItem = (indice, campo) => {
        return errors[`items.${indice}.${campo}`];
    };

    const agregarItem = () => {
        setData('items', [
            ...data.items,
            {
                productoId: '',
                descripcion: '',
                cantidad: '1',
                precioUnitario: '0',
                porcentajeImpuesto: '16',
                porcentajeDescuento: '0',
            },
        ]);
    };

    const eliminarItem = (indiceItem) => {
        if (data.items.length === 1) {
            return;
        }

        setData(
            'items',
            data.items.filter((_, indiceActual) => indiceActual !== indiceItem),
        );
    };

    const actualizarItem = (indiceItem, campo, valor) => {
        const itemsActualizados = data.items.map((itemActual, indiceActual) => {
            if (indiceActual !== indiceItem) {
                return itemActual;
            }

            return {
                ...itemActual,
                [campo]: valor,
            };
        });

        setData('items', itemsActualizados);
    };

    const seleccionarProducto = (indiceItem, productoId) => {
        const productoSeleccionado = productos.find(
            (producto) => producto.id === productoId,
        );

        const itemsActualizados = data.items.map((itemActual, indiceActual) => {
            if (indiceActual !== indiceItem) {
                return itemActual;
            }

            if (!productoSeleccionado) {
                return {
                    ...itemActual,
                    productoId: '',
                };
            }

            return {
                ...itemActual,
                productoId: productoSeleccionado.id,
                descripcion:
                    productoSeleccionado.descripcion?.trim() !== ''
                        ? productoSeleccionado.descripcion
                        : productoSeleccionado.nombre,
                precioUnitario: String(productoSeleccionado.precioBase ?? '0'),
                porcentajeImpuesto: String(
                    productoSeleccionado.porcentajeImpuesto ?? '0',
                ),
            };
        });

        setData('items', itemsActualizados);
    };

    const submit = (evento) => {
        evento.preventDefault();

        if (modoEdicion) {
            put(route('facturas.update', factura.id));
            return;
        }

        post(route('facturas.store'));
    };

    return (
        <AuthenticatedLayout
            header={
                <div className="flex items-center justify-between">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800">
                        {modoEdicion
                            ? `Editar factura ${factura?.folio ?? ''}`
                            : 'Nueva factura'}
                    </h2>
                    <Link
                        href={route('facturas.index')}
                        className="text-sm font-medium text-indigo-700 hover:text-indigo-900"
                    >
                        Volver al listado
                    </Link>
                </div>
            }
        >
            <Head title={modoEdicion ? 'Editar factura' : 'Nueva factura'} />

            <div className="py-8">
                <div className="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                    {formularioBloqueado && (
                        <div className="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            Esta factura está cancelada y no se puede editar.
                        </div>
                    )}

                    <form onSubmit={submit} className="space-y-6">
                        <div className="rounded-lg bg-white p-4 shadow sm:p-6">
                            <h3 className="text-lg font-semibold text-gray-800">
                                Datos generales
                            </h3>
                            <div className="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                                <div>
                                    <label className="text-sm font-medium text-gray-700">
                                        Cliente
                                    </label>
                                    <select
                                        value={data.clienteId}
                                        onChange={(evento) =>
                                            setData('clienteId', evento.target.value)
                                        }
                                        disabled={formularioBloqueado}
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100"
                                    >
                                        <option value="">Selecciona un cliente</option>
                                        {clientes.map((cliente) => (
                                            <option key={cliente.id} value={cliente.id}>
                                                {cliente.razonSocial}
                                            </option>
                                        ))}
                                    </select>
                                    <InputError className="mt-2" message={errors.clienteId} />
                                </div>

                                <div>
                                    <label className="text-sm font-medium text-gray-700">
                                        Fecha emisión
                                    </label>
                                    <input
                                        type="date"
                                        value={data.fechaEmision}
                                        onChange={(evento) =>
                                            setData('fechaEmision', evento.target.value)
                                        }
                                        disabled={formularioBloqueado}
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100"
                                    />
                                    <InputError className="mt-2" message={errors.fechaEmision} />
                                </div>

                                <div>
                                    <label className="text-sm font-medium text-gray-700">
                                        Fecha vencimiento
                                    </label>
                                    <input
                                        type="date"
                                        value={data.fechaVencimiento || ''}
                                        onChange={(evento) =>
                                            setData('fechaVencimiento', evento.target.value)
                                        }
                                        disabled={formularioBloqueado}
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100"
                                    />
                                    <InputError
                                        className="mt-2"
                                        message={errors.fechaVencimiento}
                                    />
                                </div>

                                <div>
                                    <label className="text-sm font-medium text-gray-700">
                                        Moneda
                                    </label>
                                    <input
                                        type="text"
                                        value={data.moneda}
                                        onChange={(evento) =>
                                            setData('moneda', evento.target.value.toUpperCase())
                                        }
                                        maxLength={3}
                                        disabled={formularioBloqueado}
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100"
                                    />
                                    <InputError className="mt-2" message={errors.moneda} />
                                </div>
                            </div>

                            <div className="mt-4">
                                <label className="text-sm font-medium text-gray-700">
                                    Notas
                                </label>
                                <textarea
                                    value={data.notas}
                                    onChange={(evento) =>
                                        setData('notas', evento.target.value)
                                    }
                                    disabled={formularioBloqueado}
                                    rows={3}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100"
                                />
                                <InputError className="mt-2" message={errors.notas} />
                            </div>
                        </div>

                        <div className="rounded-lg bg-white p-4 shadow sm:p-6">
                            <div className="flex items-center justify-between">
                                <h3 className="text-lg font-semibold text-gray-800">Items</h3>
                                <button
                                    type="button"
                                    onClick={agregarItem}
                                    disabled={formularioBloqueado}
                                    className="inline-flex items-center rounded-md border border-indigo-300 bg-indigo-50 px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-indigo-700 hover:bg-indigo-100 disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    Agregar item
                                </button>
                            </div>

                            <InputError className="mt-2" message={errors.items} />

                            <div className="mt-4 space-y-4">
                                {data.items.map((item, indiceItem) => (
                                    <div
                                        key={`item-${indiceItem}`}
                                        className="rounded-md border border-gray-200 p-4"
                                    >
                                        <div className="grid gap-3 md:grid-cols-2 lg:grid-cols-6">
                                            <div className="lg:col-span-2">
                                                <label className="text-sm font-medium text-gray-700">
                                                    Producto
                                                </label>
                                                <select
                                                    value={item.productoId}
                                                    onChange={(evento) =>
                                                        seleccionarProducto(
                                                            indiceItem,
                                                            evento.target.value,
                                                        )
                                                    }
                                                    disabled={formularioBloqueado}
                                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100"
                                                >
                                                    <option value="">Sin producto</option>
                                                    {productos.map((producto) => (
                                                        <option
                                                            key={producto.id}
                                                            value={producto.id}
                                                        >
                                                            {producto.codigo} - {producto.nombre}
                                                        </option>
                                                    ))}
                                                </select>
                                                <InputError
                                                    className="mt-2"
                                                    message={obtenerErrorItem(
                                                        indiceItem,
                                                        'productoId',
                                                    )}
                                                />
                                            </div>

                                            <div className="lg:col-span-4">
                                                <label className="text-sm font-medium text-gray-700">
                                                    Descripción
                                                </label>
                                                <input
                                                    type="text"
                                                    value={item.descripcion}
                                                    onChange={(evento) =>
                                                        actualizarItem(
                                                            indiceItem,
                                                            'descripcion',
                                                            evento.target.value,
                                                        )
                                                    }
                                                    disabled={formularioBloqueado}
                                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100"
                                                />
                                                <InputError
                                                    className="mt-2"
                                                    message={obtenerErrorItem(
                                                        indiceItem,
                                                        'descripcion',
                                                    )}
                                                />
                                            </div>

                                            <div>
                                                <label className="text-sm font-medium text-gray-700">
                                                    Cantidad
                                                </label>
                                                <input
                                                    type="number"
                                                    min="0"
                                                    step="0.001"
                                                    value={item.cantidad}
                                                    onChange={(evento) =>
                                                        actualizarItem(
                                                            indiceItem,
                                                            'cantidad',
                                                            evento.target.value,
                                                        )
                                                    }
                                                    disabled={formularioBloqueado}
                                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100"
                                                />
                                                <InputError
                                                    className="mt-2"
                                                    message={obtenerErrorItem(
                                                        indiceItem,
                                                        'cantidad',
                                                    )}
                                                />
                                            </div>

                                            <div>
                                                <label className="text-sm font-medium text-gray-700">
                                                    Precio unitario
                                                </label>
                                                <input
                                                    type="number"
                                                    min="0"
                                                    step="0.01"
                                                    value={item.precioUnitario}
                                                    onChange={(evento) =>
                                                        actualizarItem(
                                                            indiceItem,
                                                            'precioUnitario',
                                                            evento.target.value,
                                                        )
                                                    }
                                                    disabled={formularioBloqueado}
                                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100"
                                                />
                                                <InputError
                                                    className="mt-2"
                                                    message={obtenerErrorItem(
                                                        indiceItem,
                                                        'precioUnitario',
                                                    )}
                                                />
                                            </div>

                                            <div>
                                                <label className="text-sm font-medium text-gray-700">
                                                    % Impuesto
                                                </label>
                                                <input
                                                    type="number"
                                                    min="0"
                                                    max="100"
                                                    step="0.01"
                                                    value={item.porcentajeImpuesto}
                                                    onChange={(evento) =>
                                                        actualizarItem(
                                                            indiceItem,
                                                            'porcentajeImpuesto',
                                                            evento.target.value,
                                                        )
                                                    }
                                                    disabled={formularioBloqueado}
                                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100"
                                                />
                                                <InputError
                                                    className="mt-2"
                                                    message={obtenerErrorItem(
                                                        indiceItem,
                                                        'porcentajeImpuesto',
                                                    )}
                                                />
                                            </div>

                                            <div>
                                                <label className="text-sm font-medium text-gray-700">
                                                    % Descuento
                                                </label>
                                                <input
                                                    type="number"
                                                    min="0"
                                                    max="100"
                                                    step="0.01"
                                                    value={item.porcentajeDescuento}
                                                    onChange={(evento) =>
                                                        actualizarItem(
                                                            indiceItem,
                                                            'porcentajeDescuento',
                                                            evento.target.value,
                                                        )
                                                    }
                                                    disabled={formularioBloqueado}
                                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100"
                                                />
                                                <InputError
                                                    className="mt-2"
                                                    message={obtenerErrorItem(
                                                        indiceItem,
                                                        'porcentajeDescuento',
                                                    )}
                                                />
                                            </div>
                                        </div>

                                        <div className="mt-3 flex items-center justify-between">
                                            <p className="text-sm font-medium text-gray-700">
                                                Total línea:{' '}
                                                <span className="text-gray-900">
                                                    {formatearMoneda(
                                                        obtenerLineaCalculada(item),
                                                    )}
                                                </span>
                                            </p>

                                            <button
                                                type="button"
                                                onClick={() => eliminarItem(indiceItem)}
                                                disabled={
                                                    formularioBloqueado ||
                                                    data.items.length === 1
                                                }
                                                className="inline-flex items-center rounded-md border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-red-700 hover:bg-red-100 disabled:cursor-not-allowed disabled:opacity-50"
                                            >
                                                Quitar
                                            </button>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        <div className="rounded-lg bg-white p-4 shadow sm:p-6">
                            <h3 className="text-lg font-semibold text-gray-800">Resumen</h3>
                            <div className="mt-3 grid gap-3 text-sm md:grid-cols-4">
                                <div className="rounded-md bg-gray-50 p-3">
                                    <p className="text-gray-600">Subtotal</p>
                                    <p className="text-base font-semibold text-gray-900">
                                        {formatearMoneda(totalesCalculados.subtotal)}
                                    </p>
                                </div>
                                <div className="rounded-md bg-gray-50 p-3">
                                    <p className="text-gray-600">Impuesto</p>
                                    <p className="text-base font-semibold text-gray-900">
                                        {formatearMoneda(totalesCalculados.impuesto)}
                                    </p>
                                </div>
                                <div className="rounded-md bg-gray-50 p-3">
                                    <p className="text-gray-600">Descuento</p>
                                    <p className="text-base font-semibold text-gray-900">
                                        {formatearMoneda(totalesCalculados.descuento)}
                                    </p>
                                </div>
                                <div className="rounded-md bg-indigo-50 p-3">
                                    <p className="text-indigo-700">Total</p>
                                    <p className="text-base font-semibold text-indigo-900">
                                        {formatearMoneda(totalesCalculados.total)}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div className="flex justify-end gap-3">
                            <Link
                                href={route('facturas.index')}
                                className="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50"
                            >
                                Cancelar
                            </Link>
                            <PrimaryButton
                                disabled={processing || formularioBloqueado}
                                type="submit"
                            >
                                {modoEdicion ? 'Actualizar factura' : 'Guardar factura'}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
