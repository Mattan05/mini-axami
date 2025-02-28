import { useParams } from "react-router-dom";

function CustomerProfile() {
    const {id} = useParams();
    return ( <>
        <h1>Customer Id: {id}</h1>
    </> );
}

export default CustomerProfile;