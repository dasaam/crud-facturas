import DangerButton from '@/Components/DangerButton';
import PrimaryButton from '@/Components/PrimaryButton';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm } from '@inertiajs/react';

function formatearMoneda(cantidad) {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN',
    }).format(cantidad ?? 0);
}

const opcionesEstado = [
    { valor: '', etiqueta: 'Todos' },
    { valor: 'borrador', etiqueta: 'Borrador' },
    { valor: 'emitida', etiqueta: 'Emitida' },
    { valor: 'pagada', etiqueta: 'Pagada' },
    { valor: 'cancelada', etiqueta: 'Cancelada' },
];

const opcionesCantidadPorPagina = ['10', '15', '25', '50'];

export default function Index({ facturasPaginadas, clientes, filtros }) {
    const { data, setData, get, processing } = useForm({
        filtroBusqueda: filtros.filtroBusqueda ?? '',
        filtroEstado: filtros.filtroEstado ?? '',
        filtroClienteId: filtros.filtroClienteId ?? '',
        cantidadPorPagina: filtros.cantidadPorPagina ?? '10',
    });

    const submit = (evento) => {
        evento.preventDefault();
        get(route('facturas.index'), {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const limpiarFiltros = () => {
        const filtrosLimpios = {
            filtroBusqueda: '',
            filtroEstado: '',
            filtroClienteId: '',
            cantidadPorPagina: data.cantidadPorPagina || '10',
        };

        setData(filtrosLimpios);
        router.get(route('facturas.index'), filtrosLimpios, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const cancelarFactura = (idFactura, folioFactura) => {
        const usuarioConfirmo = window.confirm(
            `Se cancelará la factura ${folioFactura}. Esta acción no la elimina. ¿Deseas continuar?`,
        );

        if (!usuarioConfirmo) {
            return;
        }

        router.patch(route('facturas.cancelar', idFactura), {}, { preserveScroll: true });
    };

    const facturarFactura = (idFactura, folioFactura) => {
        const usuarioConfirmo = window.confirm(
            `Se marcará como emitida la factura ${folioFactura}. ¿Deseas continuar?`,
        );

        if (!usuarioConfirmo) {
            return;
        }

        router.patch(route('facturas.facturar', idFactura), {}, { preserveScroll: true });
    };

    return (
        <AuthenticatedLayout
            header={
                <div className="flex items-center justify-between">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800">
                        Facturas
                    </h2>
                    <Link href={route('facturas.create')}>
                        <PrimaryButton>Nueva factura</PrimaryButton>
                    </Link>
                </div>
            }
        >
            <Head title="Facturas" />

            <div className="py-8">
                <div className="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                    <div className="rounded-lg bg-white p-4 shadow sm:p-6">
                        <form onSubmit={submit} className="grid gap-4 md:grid-cols-5">
                            <div className="md:col-span-2">
                                <label className="text-sm font-medium text-gray-700">
                                    Buscar
                                </label>
                                <input
                                    type="text"
                                    value={data.filtroBusqueda}
                                    onChange={(evento) =>
                                        setData('filtroBusqueda', evento.target.value)
                                    }
                                    placeholder="Folio o cliente"
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>

                            <div>
                                <label className="text-sm font-medium text-gray-700">
                                    Estado
                                </label>
                                <select
                                    value={data.filtroEstado}
                                    onChange={(evento) =>
                                        setData('filtroEstado', evento.target.value)
                                    }
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    {opcionesEstado.map((opcionEstado) => (
                                        <option
                                            key={opcionEstado.valor || 'todos'}
                                            value={opcionEstado.valor}
                                        >
                                            {opcionEstado.etiqueta}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            <div>
                                <label className="text-sm font-medium text-gray-700">
                                    Cliente
                                </label>
                                <select
                                    value={data.filtroClienteId}
                                    onChange={(evento) =>
                                        setData('filtroClienteId', evento.target.value)
                                    }
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">Todos</option>
                                    {clientes.map((cliente) => (
                                        <option key={cliente.id} value={cliente.id}>
                                            {cliente.razonSocial}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            <div>
                                <label className="text-sm font-medium text-gray-700">
                                    Por página
                                </label>
                                <select
                                    value={data.cantidadPorPagina}
                                    onChange={(evento) =>
                                        setData('cantidadPorPagina', evento.target.value)
                                    }
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    {opcionesCantidadPorPagina.map((opcion) => (
                                        <option key={opcion} value={opcion}>
                                            {opcion}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            <div className="md:col-span-5 flex gap-3">
                                <PrimaryButton disabled={processing}>Aplicar filtros</PrimaryButton>
                                <button
                                    type="button"
                                    onClick={limpiarFiltros}
                                    className="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50"
                                >
                                    Limpiar
                                </button>
                            </div>
                        </form>
                    </div>

                    <div className="overflow-hidden rounded-lg bg-white shadow">
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th className="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                                            Folio
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                                            Cliente
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                                            Fecha
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                                            Total
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                                            Estado
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100 bg-white">
                                    {facturasPaginadas.data.length === 0 && (
                                        <tr>
                                            <td
                                                colSpan={6}
                                                className="px-4 py-8 text-center text-sm text-gray-500"
                                            >
                                                No hay facturas registradas.
                                            </td>
                                        </tr>
                                    )}

                                    {facturasPaginadas.data.map((factura) => (
                                        <tr key={factura.id}>
                                            <td className="px-4 py-3 text-sm font-medium text-gray-900">
                                                {factura.folio}
                                            </td>
                                            <td className="px-4 py-3 text-sm text-gray-700">
                                                {factura.cliente?.razonSocial}
                                            </td>
                                            <td className="px-4 py-3 text-sm text-gray-700">
                                                {factura.fechaEmision}
                                            </td>
                                            <td className="px-4 py-3 text-sm text-gray-700">
                                                {formatearMoneda(factura.total)}
                                            </td>
                                            <td className="px-4 py-3 text-sm">
                                                <span className="rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold uppercase text-gray-700">
                                                    {factura.estado}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3 text-sm">
                                                <div className="flex flex-wrap gap-2">
                                                    {factura.estado !== 'cancelada' && (
                                                        <Link
                                                            href={route('facturas.edit', factura.id)}
                                                            className="inline-flex items-center rounded-md border border-indigo-300 bg-indigo-50 px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-indigo-700 hover:bg-indigo-100"
                                                        >
                                                            Editar
                                                        </Link>
                                                    )}

                                                    {factura.estado === 'borrador' && (
                                                        <button
                                                            type="button"
                                                            onClick={() =>
                                                                facturarFactura(
                                                                    factura.id,
                                                                    factura.folio,
                                                                )
                                                            }
                                                            className="inline-flex items-center rounded-md border border-emerald-300 bg-emerald-50 px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-emerald-700 hover:bg-emerald-100"
                                                        >
                                                            Facturar
                                                        </button>
                                                    )}

                                                    {factura.estado !== 'cancelada' && (
                                                        <DangerButton
                                                            type="button"
                                                            onClick={() =>
                                                                cancelarFactura(
                                                                    factura.id,
                                                                    factura.folio,
                                                                )
                                                            }
                                                        >
                                                            Cancelar
                                                        </DangerButton>
                                                    )}
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        <div className="flex flex-wrap items-center gap-2 border-t border-gray-100 px-4 py-4">
                            {facturasPaginadas.links.map((enlacePaginacion) => {
                                if (!enlacePaginacion.url) {
                                    return (
                                        <span
                                            key={enlacePaginacion.label}
                                            className="rounded border border-gray-200 px-3 py-1.5 text-sm text-gray-400"
                                            dangerouslySetInnerHTML={{
                                                __html: enlacePaginacion.label,
                                            }}
                                        />
                                    );
                                }

                                return (
                                    <Link
                                        key={enlacePaginacion.label}
                                        href={enlacePaginacion.url}
                                        className={`rounded border px-3 py-1.5 text-sm ${
                                            enlacePaginacion.active
                                                ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                                                : 'border-gray-300 text-gray-700 hover:bg-gray-50'
                                        }`}
                                        dangerouslySetInnerHTML={{
                                            __html: enlacePaginacion.label,
                                        }}
                                    />
                                );
                            })}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
