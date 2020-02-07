import axios from "axios";

const deleteFile = (url, token) => {
	axios.delete(url, {
		data: {
			token: token
		},
		headers: {
			'Content-Type': 'application/json'
		}
	})
		.then((res) => {
			console.log(res);
		})
		.catch((err) => {
			console.error(err);
		});
};

export default deleteFile;