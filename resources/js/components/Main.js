const Main = props => {
    return (
        <main
            className={`bg-white shadow rounded-md py-6 px-4 sm:px-6 lg:px-8 mt-3 ${
                props.className ? props.className : ''
            }`}
        >
            {props.children}
        </main>
    )
}

export default Main
