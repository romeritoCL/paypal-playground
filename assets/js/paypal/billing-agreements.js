import JSONEditor from 'jsoneditor';
import 'jsoneditor/dist/jsoneditor.css';
import createToken from './billing-agreements/create-token';
window.JSONEditor = JSONEditor;

$("#collapseOne").addClass('show');
createToken.startEditor();